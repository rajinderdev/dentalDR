<?php

namespace App\Services;

use App\Models\PatientTreatmentsDone;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\PatientTreatmentsDoneResource;
use App\Models\Patient;
use App\Models\WaitingAreaPatient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PatientPackageUsage;
use App\Helpers\EntityDataHelper;

class PatientTreatmentsDoneService
{
    /**
     * Get a single treatment done by ID
     *
     * @param string $treatmentDoneId
     * @return PatientTreatmentsDone|null
     */
    public function getTreatmentDoneById(string $treatmentDoneId): ?PatientTreatmentsDone
    {
        $treatment = PatientTreatmentsDone::find($treatmentDoneId);
         $children = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $treatment->PatientTreatmentDoneID)->get();
         $treatment->children = $children;
         return $treatment;
    }

    /**
     * Get a paginated list of Patient Treatments Done.
     *
     * @param int $perPage
     * @return array
     */
    public function getPatientTreatmentsDone(Patient $patient, int $perPage, string $status, $startDate = null, $endDate = null): array
    {
        // Fetch all treatments for the patient
        $allTreatments = PatientTreatmentsDone::where('PatientID', $patient->id)
            ->when($status == 'ongoing', function ($query) {
                return $query->where('IsArchived', 0);
            })
            ->when($status == 'completed', function ($query) {
                return $query->where('IsArchived', 1);
            })
            // ->when($startDate, function ($query) use ($startDate) {
            //     return $query->whereDate('TreatmentDate', '>=', $startDate);
            // })
            // ->when($endDate, function ($query) use ($endDate) {
            //     return $query->whereDate('TreatmentDate', '<=', $endDate);
            // })
            ->orderBy('TreatmentDate', 'desc')
            ->get();

        // Group treatments by ParentPatientTreatmentDoneID
        $parents = $allTreatments->where('ParentPatientTreatmentDoneID', '00000000-0000-0000-0000-000000000000')->values();

        // Attach children to their respective parents
        $parents = $parents->map(function ($parent) use ($allTreatments) {
            $children = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $parent->PatientTreatmentDoneID)->orderBy('TreatmentDate', 'desc')->get();
            $parent->children = $children;
            return $parent;
        });
        
        // Paginate the parent treatments
        $page = request('page', 1);
        $perPage = $perPage > 0 ? $perPage : 50;
        $paginated = new LengthAwarePaginator(
            $parents->forPage($page, $perPage)->values(),
            $parents->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $data = $paginated;

        return [
            'patient_treatments_done' => $data, // Transform the data using the resource
            'pagination' => [
                'currentPage' => $data->currentPage(),
                'perPage' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new patient treatments done record.
     *
     * @param array $data The validated data for creating the patient treatments done
     * @return PatientTreatmentsDone The newly created patient treatments done model
     */
    public function createTreatmentDone(array $data, Patient $patient): PatientTreatmentsDone
    {
        return DB::transaction(function () use ($data, $patient) {
            // Helper function to safely get string values
            $getSafeValue = function($value, $type = 'string') {
                if (is_array($value)) {
                    return null;
                }
                if ($value === '' || $value === null) {
                    return null;
                }
                if ($type === 'numeric' && !is_numeric($value)) {
                    return null;
                }
                return $value;
            };
            $authUser = Auth::user();
            $userID = $authUser && !empty($authUser->UserID) ? $authUser->UserID : null;
            if(!array_key_exists('WaitingAreaID',$data) || is_null($data['WaitingAreaID'])) {
                $TreatmentTotalCost = $getSafeValue($data['TreatmentCost'] ?? null, 'numeric') - $getSafeValue($data['TreatmentDiscount'] ?? null, 'numeric')+$getSafeValue($data['TreatmentAddition'] ?? null, 'numeric');
                $patientTreatmentsDone = $patient->patient_treatments_done()->create([
                    'PatientID' => $patient->id,
                    'ProviderID' => $data['ProviderID'],
                    'ParentPatientTreatmentDoneID' => $getSafeValue($data['ParentPatientTreatmentDoneID'] ?? '00000000-0000-0000-0000-000000000000'),
                    'TreatmentCost' => $getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
                    'TreatmentDiscount' => $getSafeValue($data['TreatmentDiscount'] ?? null, 'numeric'),
                    'TreatmentBalance' => $getSafeValue($data['TreatmentBalance'] ?? null, 'numeric'),
                    'TreatmentAddition' => $getSafeValue($data['TreatmentAddition'] ?? null),
                    'TreatmentTotalCost' => $TreatmentTotalCost,
                    'TreatmentPayment' => $getSafeValue($data['TreatmentPayment'] ?? null),
                    'TreatmentDate' => $data['TreatmentDate']?$data['TreatmentDate']:now()->toDateTimeString(),
                    'TeethTreatmentNote' => $data['TeethTreatmentNote']?? null,
                    "AddedBy" => $data['AddedBy'] ?? $userID,
                    "LastUpdatedBy" => $data['LastUpdatedBy'] ?? $userID,
                    "rowguid" =>  $data['rowguid'] ?? strtoupper(Str::uuid()->toString()),
                    'WaitingAreaFlag' => 0
                ]);

                // Handle TreatmentSteps array - create multiple treatment type records
                if (isset($data['TreatmentSteps']) && is_array($data['TreatmentSteps']) && count($data['TreatmentSteps']) > 0) {
                    foreach ($data['TreatmentSteps'] as $step) {
                        $patientTreatmentsDone->treatment_type()->create([
                            'TreatmentTypeID' => $step['TreatmentTypeID'],
                            'TreatmentSubTypeID' => $getSafeValue($step['TreatmentSubTypeID'] ?? '00000000-0000-0000-0000-000000000000'),
                            'TeethTreatment' => $getSafeValue(json_encode($data['TeethTreatment'] ?? null)),
                            'TeethTreatmentNote' => $getSafeValue($step['TeethTreatmentNote'] ?? null),
                            'TreatmentCost' => $getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
                            'TreatmentTotalCost' => $TreatmentTotalCost,
                            'Discount' => $getSafeValue($data['TreatmentDiscount'] ?? null, 'numeric'),
                            'Addition' => $getSafeValue($step['TreatmentAddition'] ?? null),
                        ]);
                    }
                }
                // Handle single treatment when TreatmentSteps is empty or not provided
                elseif (isset($data['TreatmentTypeID'])) {
                    $patientTreatmentsDone->treatment_type()->create([
                        'TreatmentTypeID' => $data['TreatmentTypeID'],
                        'TreatmentSubTypeID' => $getSafeValue($data['TreatmentSubTypeID'] ?? '00000000-0000-0000-0000-000000000000'),
                        'TeethTreatment' => $getSafeValue(json_encode($data['TeethTreatment'] ?? null)),
                        'TeethTreatmentNote' => $getSafeValue($data['TeethTreatmentNote'] ?? null),
                        'TreatmentCost' => $getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
                        'TreatmentTotalCost' => $TreatmentTotalCost,
                        'Discount' => $getSafeValue($data['TreatmentDiscount'] ?? null, 'numeric'),
                        'Addition' => $getSafeValue($data['TreatmentAddition'] ?? null),
                    ]);
                }
            } else {
                $waitingAreaPatient = WaitingAreaPatient::where('WaitingAreaID',$data['WaitingAreaID'])->first();
                
                    $patientTreatmentsDone = $patient->patient_treatments_done()->create([
                        'PatientID' => $patient->id,
                        'ProviderID' => $data['ProviderID'],
                        'WaitingAreaID' => $data['WaitingAreaID'],
                        'TreatmentDate' => $data['TreatmentDate'] ?? Carbon::now(),
                        'TeethTreatmentNote'=>$waitingAreaPatient?$waitingAreaPatient->Comments:null,
                        'ParentPatientTreatmentDoneID' => $getSafeValue($data['ParentPatientTreatmentDoneID'] ?? '00000000-0000-0000-0000-000000000000'),
                         "rowguid" =>  $data['rowguid'] ?? strtoupper(Str::uuid()->toString())
                    ]);
              
               

                if ($patientTreatmentsDone->waiting_area) {
                    $patientTreatmentsDone->waiting_area->update(['MovedToTreatmentArea' => 1]);
                }
            }
            if (array_key_exists('PatientPackageID', $data) && $data['PatientPackageID']) {
                $packageUsageData = [
                    'PatientID' => $patient->PatientID,
                    'PatientPackageID' => $data['PatientPackageID'],
                    'PatientTreatmentDoneID' => $patientTreatmentsDone->PatientTreatmentDoneID,
                    'ProviderID' => $data['ProviderID'] ?? Auth::id(),
                    'TreatmentDate' => now(),
                    'Notes' => 'Auto-created from treatment',
                    'CreatedBy' => Auth::id(),
                    'ClinicID' => $data['ClinicID'] ?? Auth::user()->ClinicID
                ];
                $dataToPersist = EntityDataHelper::prepareForCreation($packageUsageData);
        
                $dataToPersist['ClinicID'] = $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID;
                $dataToPersist['CreatedBy'] = Auth::id();
        
                PatientPackageUsage::create($dataToPersist);
            }

            return $patientTreatmentsDone;
        });
    }
    /**
     * Create a new patient treatments done record.
     *
     * @param array $data The validated data for creating the patient treatments done
     * @return PatientTreatmentsDone The newly created patient treatments done model
     */
    
    public function createTreatmentDoneFromWaitingArea(array $data, Patient $patient): PatientTreatmentsDone
    {
        return DB::transaction(function () use ($data, $patient) {
            $authUser = Auth::user();
            $userID = $authUser?->UserID;

            if ($this->shouldCreateNewTreatment($data)) {
                return $this->handleNewTreatmentCreation($data, $patient, $userID);
            }

            return $this->handleWaitingAreaTreatment($data, $patient, $userID);
        });
    }

    private function shouldCreateNewTreatment(array $data): bool
    {
        return !array_key_exists('WaitingAreaID', $data) || is_null($data['WaitingAreaID']);
    }

   private function handleNewTreatmentCreation(array $data, Patient $patient, ?string $userID): PatientTreatmentsDone
    {
        $isExisting = PatientTreatmentsDone::where('PatientTreatmentDoneID', $data['ParentPatientTreatmentDoneID'])
            ->whereNotNull('TreatmentCost')
            ->where('PatientID', $patient->id)
            ->count();
        $data['PatientID']=$patient->id;
        $treatmentData = $this->prepareTreatmentData($data, $userID, $isExisting);
        
        try {
            if ($isExisting > 0) {
                // If treatment exists with cost, create a new one
                if(array_key_exists('TreatmentSteps', $data) && count($data['TreatmentSteps']) ==0){
                  $treatmentData['ParentPatientTreatmentDoneID']='00000000-0000-0000-0000-000000000000';
                }
                $patientTreatmentsDone = $patient->patient_treatments_done()->create($treatmentData);
                if(array_key_exists('PatientPackageID',$data) && $data['PatientPackageID']){
                        $packageUsageData = [
                        'PatientID' => $patient->PatientID,
                        'PatientPackageID' => $data['PatientPackageID'],
                        'PatientTreatmentDoneID' => $patientTreatmentsDone->PatientTreatmentDoneID,
                        'ProviderID' => $data['ProviderID'] ?? Auth::id(),
                        'TreatmentDate' => now(),
                        'Notes' => 'Auto-created from treatment',
                        'CreatedBy' => Auth::id(),
                        'ClinicID' => $data['ClinicID'] ?? Auth::user()->ClinicID
                    ];
                    $dataToPersist = EntityDataHelper::prepareForCreation($packageUsageData);
            
                    $dataToPersist['ClinicID'] = $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID;
                    $dataToPersist['CreatedBy'] = Auth::id();
                    PatientPackageUsage::create($dataToPersist);
                }
            } else {
                // Try to find and update existing treatment
                $existing = PatientTreatmentsDone::where('PatientTreatmentDoneID', $data['ParentPatientTreatmentDoneID'])
                    ->where('PatientID', $patient->id)
                    ->first();
                    
                if ($existing) {
                    unset($treatmentData['ParentPatientTreatmentDoneID']);
                    $existing->update($treatmentData);
                    $patientTreatmentsDone = $existing;
                    if(array_key_exists('PatientPackageID',$data) && $data['PatientPackageID']){
                        $packageUsageData = [
                            'PatientID' => $patient->PatientID,
                            'PatientPackageID' => $data['PatientPackageID'],
                            'PatientTreatmentDoneID' => $patientTreatmentsDone->PatientTreatmentDoneID,
                            'ProviderID' => $data['ProviderID'] ?? Auth::id(),
                            'TreatmentDate' => now(),
                            'Notes' => 'Auto-created from treatment',
                            'CreatedBy' => Auth::id(),
                            'ClinicID' => $data['ClinicID'] ?? Auth::user()->ClinicID
                        ];
                        $dataToPersist = EntityDataHelper::prepareForCreation($packageUsageData);
                
                        $dataToPersist['ClinicID'] = $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID;
                        $dataToPersist['CreatedBy'] = Auth::id();
            
                        PatientPackageUsage::create($dataToPersist);
                    }
                } else {
                    // If no existing treatment found, create a new one
                    $patientTreatmentsDone = $patient->patient_treatments_done()->create($treatmentData);
                    if(array_key_exists('PatientPackageID',$data) && $data['PatientPackageID']){
                         $packageUsageData = [
                            'PatientID' => $patient->PatientID,
                            'PatientPackageID' => $data['PatientPackageID'],
                            'PatientTreatmentDoneID' => $patientTreatmentsDone->PatientTreatmentDoneID,
                            'ProviderID' => $data['ProviderID'] ?? Auth::id(),
                            'TreatmentDate' => now(),
                            'Notes' => 'Auto-created from treatment',
                            'CreatedBy' => Auth::id(),
                            'ClinicID' => $data['ClinicID'] ?? Auth::user()->ClinicID
                        ];
                        $dataToPersist = EntityDataHelper::prepareForCreation($packageUsageData);
                
                        $dataToPersist['ClinicID'] = $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID;
                        $dataToPersist['CreatedBy'] = Auth::id();
                        PatientPackageUsage::create($dataToPersist);
                    }
                }
            }

            $this->handleTreatmentSteps($data, $patientTreatmentsDone);
            return $patientTreatmentsDone;

        } catch (\Exception $e) {
            Log::error('Error in handleNewTreatmentCreation: ' . $e->getMessage());
            throw $e;
        }
    }


    private function prepareTreatmentData(array $data, ?string $userID,$isExisting): array
    {
        $baseData = [
            'ProviderID' => $data['ProviderID'],
            'ParentPatientTreatmentDoneID' => $data['ParentPatientTreatmentDoneID'] ?? '00000000-0000-0000-0000-000000000000',
            'TreatmentCost' => $this->getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
            'TreatmentDiscount' => $this->getSafeValue($data['TreatmentDiscount'] ?? null, 'numeric'),
            'TreatmentBalance' => $this->getSafeValue($data['TreatmentBalance'] ?? null, 'numeric'),
            'TreatmentAddition' => $this->getSafeValue($data['TreatmentAddition'] ?? null),
            'TreatmentTotalCost' => $this->getSafeValue($data['TreatmentTotalCost'] ?? null, 'numeric'),
            'TreatmentPayment' => $this->getSafeValue($data['TreatmentPayment'] ?? null),
            'TreatmentDate' => $data['TreatmentDate'] ?? now(),
            'TeethTreatmentNote' => $data['TeethTreatmentNote'] ?? null,
            'AddedBy' => $data['AddedBy'] ?? $userID,
            'LastUpdatedBy' => $data['LastUpdatedBy'] ?? $userID,
            'rowguid' => $data['rowguid'] ?? strtoupper(Str::uuid()->toString()),
            'WaitingAreaFlag' => 0
        ];

        return $isExisting >0
            ? array_merge($baseData, ['PatientID' => $data['PatientID']])
            : $baseData;
    }

   

    private function handleTreatmentSteps(array $data, PatientTreatmentsDone $treatment): void
    {
        if (!empty($data['TreatmentSteps']) && is_array($data['TreatmentSteps'])) {
            $this->createMultipleTreatmentSteps($data, $treatment);
        } elseif (isset($data['TreatmentTypeID'])) {
            $this->createSingleTreatmentStep($data, $treatment);
        }
    }

    private function createMultipleTreatmentSteps(array $data, PatientTreatmentsDone $treatment): void
    {
        foreach ($data['TreatmentSteps'] as $step) {
            $this->createTreatmentStep($treatment, [
                'TreatmentTypeID' => $step['TreatmentTypeID'],
                'TreatmentSubTypeID' => $step['TreatmentSubTypeID'] ?? '00000000-0000-0000-0000-000000000000',
                'TeethTreatmentNote' => $step['TeethTreatmentNote'] ?? null,
                'Addition' => $step['TreatmentAddition'] ?? null,
            ], $data);
        }
    }

    private function createSingleTreatmentStep(array $data, PatientTreatmentsDone $treatment): void
    {
        $this->createTreatmentStep($treatment, [
            'TreatmentTypeID' => $data['TreatmentTypeID'],
            'TreatmentSubTypeID' => $data['TreatmentSubTypeID'] ?? '00000000-0000-0000-0000-000000000000',
            'TeethTreatmentNote' => $data['TeethTreatmentNote'] ?? null,
            'Addition' => $data['TreatmentAddition'] ?? null,
        ], $data);
    }

    private function createTreatmentStep(PatientTreatmentsDone $treatment, array $stepData, array $data): void
    {
        $treatment->treatment_type()->create([
            'TreatmentTypeID' => $stepData['TreatmentTypeID'],
            'TreatmentSubTypeID' => $this->getSafeValue($stepData['TreatmentSubTypeID']),
            'TeethTreatment' => $this->getSafeValue(json_encode($data['TeethTreatment'] ?? null)),
            'TeethTreatmentNote' => $this->getSafeValue($stepData['TeethTreatmentNote']),
            'TreatmentCost' => $this->getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
            'TreatmentTotalCost' => $this->getSafeValue($data['TreatmentTotalCost'] ?? null, 'numeric'),
            'Discount' => $this->getSafeValue($data['TreatmentDiscount'] ?? null, 'numeric'),
            'Addition' => $this->getSafeValue($stepData['Addition']),
        ]);
    }

    private function handleWaitingAreaTreatment(array $data, Patient $patient, ?string $userID): PatientTreatmentsDone
    {
        $patientTreatmentsDone = PatientTreatmentsDone::where('PatientID', $patient->id)
            ->where('WaitingAreaID', $data['WaitingAreaID'])
            ->first();
            $waitingAreaPatient = WaitingAreaPatient::where('WaitingAreaID', $data['WaitingAreaID'])->first();
        if ($patientTreatmentsDone) {
            $patientTreatmentsDone->update(['WaitingAreaFlag' => 0]);
            $waitingAreaPatient->update(['MovedToTreatmentArea' => 1,'PatientTreatmentDoneID' => NULL]);
            return $patientTreatmentsDone;
        }

        
        
        $treatment = $patient->patient_treatments_done()->create([
            'ProviderID' => $data['ProviderID'],
            'WaitingAreaID' => $data['WaitingAreaID'],
            'TreatmentDate' => $data['TreatmentDate'] ?? now(),
            'TeethTreatmentNote' => $waitingAreaPatient?->Comments,
            'ParentPatientTreatmentDoneID' => $data['ParentPatientTreatmentDoneID'] ?? '00000000-0000-0000-0000-000000000000',
            'rowguid' => $data['rowguid'] ?? strtoupper(Str::uuid()->toString()),
            'AddedBy' => $userID,
            'LastUpdatedBy' => $userID,
        ]);

        if ($treatment->waiting_area) {
            $treatment->waiting_area->update(['MovedToTreatmentArea' => 1]);
        }

        return $treatment;
    }

    private function getSafeValue($value, string $type = 'string')
    {
        if (is_array($value)) {
            return null;
        }
        if ($value === '' || $value === null) {
            return null;
        }
        if ($type === 'numeric' && !is_numeric($value)) {
            return null;
        }
        return $value;
    }
    /**
     * Update an existing patient treatments done record.
     *
     * @param string $patientTreatmentsDone The patient treatments done model to update
     * @param array $data The validated data for updating the patient treatments done
     * @return PatientTreatmentsDone The updated patient treatments done model
     */
  public function updateTreatmentDone($patientTreatmentsDone, array $data): PatientTreatmentsDone
    {
      
        $patientTreatments = PatientTreatmentsDone::where('PatientTreatmentDoneID', $patientTreatmentsDone)
            ->with(['treatment_type']) // Eager load existing treatment types
            ->firstOrFail();

        // Make a copy of the data array to avoid modifying the original
        $updateData = $data;
        if(array_key_exists('TreatmentPayment', $updateData)){
            unset($updateData['TreatmentDate']);
            if(array_key_exists('TreatmentDate', $updateData) && !$updateData['TreatmentDate']){
                unset($updateData['TreatmentDate']); 
            }
            if(array_key_exists('IsArchived', $updateData) && !$updateData['IsArchived']){
                unset($updateData['IsArchived']); 
            }
            if(array_key_exists('ParentPatientTreatmentDoneID', $updateData) && $updateData['ParentPatientTreatmentDoneID']){
                unset($updateData['ParentPatientTreatmentDoneID']); 
            }
            if($patientTreatments){
                $previousTreatmentPayment= $patientTreatments->TreatmentTotalCost;
                $balance=$previousTreatmentPayment-$updateData['TreatmentPayment'];
                $updateData['TreatmentBalance'] = $balance;
                $updateData['TreatmentBalance']= $updateData['TreatmentBalance'] - (int)$updateData['TreatmentDiscount'];
                $updateData['TreatmentBalance']= $updateData['TreatmentBalance'] + (int)$updateData['TreatmentAddition'];
            }
        }
        // Handle waiting area updates if IsDeleted is present
        if (array_key_exists('IsDeleted', $updateData)) {
            if ($patientTreatments->waiting_area) {
                $patientTreatments->waiting_area->update([
                    'MovedToTreatmentArea' => 0,
                    'PatientTreatmentDoneID' => $patientTreatments->PatientTreatmentDoneID
                ]);
                $patientTreatments->update(['WaitingAreaFlag' => 1]);
            }
            unset($updateData['IsDeleted']); 
        }

        // Update main treatment record if there's data to update
        if (!empty($updateData)) {
            $patientTreatments->update($updateData);
        }
        // Handle TreatmentSteps - Only create if they don't exist
        if (isset($data['TreatmentSteps']) || isset($data['TreatmentTypeID'])) {
            // Get existing treatment types to compare
           
            $newTypes = [];
            
            // Handle multiple treatment steps
            if (isset($data['TreatmentSteps']) && is_array($data['TreatmentSteps'])) {
                $patientTreatments->treatment_type()->delete();
                foreach ($data['TreatmentSteps'] as $step) {
                    $typedata = $this->prepareTreatmentTypeData($data, $step);

                    $patientTreatments->treatment_type()->create($typedata);
                }
            } 
            // Handle single treatment
            elseif (isset($data['TreatmentTypeID'])) {
                $typedata  =$this->prepareTreatmentTypeData($data);
                $patientTreatments->treatment_type()->create($typedata);

            }
            
          
        }

        // Handle waiting area patient creation if this is a new treatment
        if (isset($data['WaitingAreaID']) && !$patientTreatments->exists) {
            $waitingAreaPatient = WaitingAreaPatient::where('WaitingAreaID', $data['WaitingAreaID'])->first();
            
            $patientTreatments = $patientTreatments->patient->patient_treatments_done()->create([
                'PatientID' => $patientTreatments->patient->id,
                'ProviderID' => $data['ProviderID'],
                'WaitingAreaID' => $data['WaitingAreaID'],
                'TreatmentDate' => $data['TreatmentDate'] ?? now(),
                'TeethTreatmentNote' => $waitingAreaPatient ? $waitingAreaPatient->Comments : null,
                'ParentPatientTreatmentDoneID' => $data['ParentPatientTreatmentDoneID'] ?? '00000000-0000-0000-0000-000000000000',
                'rowguid' => $data['rowguid'] ?? strtoupper(Str::uuid()->toString())
            ]);

            if ($patientTreatments->waiting_area) {
                $patientTreatments->waiting_area->update(['MovedToTreatmentArea' => 1]);
            }
        }
      
        return $patientTreatments->fresh(['treatment_type']);
    }


    /**
     * Helper method to prepare treatment type data
     */
    private function prepareTreatmentTypeData(array $baseData, array $stepData = []): array
    {
        $data = array_merge($baseData, $stepData);
        
        return [
            'TreatmentTypeID' => $data['TreatmentTypeID'],
            'TreatmentSubTypeID' => $this->getSafeValue($data['TreatmentSubTypeID'] ?? '00000000-0000-0000-0000-000000000000'),
            'TeethTreatment' => $this->getSafeValue(json_encode($data['TeethTreatment'] ?? null)),
            'TeethTreatmentNote' => $this->getSafeValue($data['TeethTreatmentNote'] ?? null),
            'TreatmentCost' => $this->getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
            'TreatmentTotalCost' => $data['TreatmentTotalCost'] ?? 0,
            'Discount' => $this->getSafeValue($data['TreatmentDiscount'] ?? $data['Discount'] ?? null, 'numeric'),
            'Addition' => $this->getSafeValue($data['TreatmentAddition'] ?? $data['Addition'] ?? null),
        ];
    }

    /**
     * Helper method to create treatment type
     */
    private function createTreatmentType($treatment, array $data): void
    {
        $treatment->treatment_type()->create([
            'TreatmentTypeID' => $data['TreatmentTypeID'],
            'TreatmentSubTypeID' => $this->getSafeValue($data['TreatmentSubTypeID'] ?? '00000000-0000-0000-0000-000000000000'),
            'TeethTreatment' => $this->getSafeValue(json_encode($data['TeethTreatment'] ?? null)),
            'TeethTreatmentNote' => $this->getSafeValue($data['TeethTreatmentNote'] ?? null),
            'TreatmentCost' => $this->getSafeValue($data['TreatmentCost'] ?? null, 'numeric'),
            'TreatmentTotalCost' => $data['TreatmentTotalCost'] ?? 0,
            'Discount' => $this->getSafeValue($data['TreatmentDiscount'] ?? $data['Discount'] ?? null, 'numeric'),
            'Addition' => $this->getSafeValue($data['TreatmentAddition'] ?? $data['Addition'] ?? null),
        ]);
    }
    public function treatmentDoneMarkCompleted(string $patientTreatmentsDoneId): PatientTreatmentsDone
    {
        $treatmentDone = PatientTreatmentsDone::where('PatientTreatmentDoneID', $patientTreatmentsDoneId)->firstOrFail();

        $treatmentDone->update([
            'IsCompleted' => 1,
        ]);

        return $treatmentDone;
    }
}