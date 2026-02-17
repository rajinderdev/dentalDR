<?php

namespace App\Services;

use App\Http\Resources\PatientWithAppointmentResource;
use App\Models\Patient;
use App\Models\Building;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PatientService
{
  

    public function createPatient(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            $data['RegistrationDate'] = array_key_exists('RegistrationDate', $data) && $data['RegistrationDate']? Carbon::parse($data['RegistrationDate']) : Carbon::now();
            $patient = Patient::create($data);
            // Check if building with same details already exists
            $building = Building::where('building_name', $data['Building'] ?? '')
                ->where('address1', $data['AddressLine1'] ?? '')
                ->where('city', $data['City'] ?? '')
                ->where('state', $data['State'] ?? '')
                ->where('country', $data['Country'] ?? '')
                ->where('pincode', $data['ZipCode'] ?? '')
                ->where('IsDeleted', false)
                ->first();
            if (!$building && $data['Building']) {
                $building = Building::create([
                    'building_name' => $data['Building'] ?? null,
                    'building_code' => $data['building_code'] ?? null,
                    'address1' => $data['AddressLine1'] ?? null,
                    'address2' => $data['AddressLine2'] ?? null,
                    'area' => $data['Area'] ?? null,
                    'city' => $data['City'] ?? null,
                    'state' => $data['State'] ?? null,
                    'country' => $data['Country'] ?? null,
                    'pincode' => $data['ZipCode'] ?? null,
                    'status' => true,
                    'IsDeleted' => false,
                    'CreatedOn' => now(),
                    'CreatedBy' => $data['CreatedBy'] ?? null,
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => $data['LastUpdatedBy'] ??null 
                ]);
            }
            $patient->building_id = $building?->id;
            $patient->save();
            $patient->patient_addresses()->create([
                'PatientID' => $patient->PatientID,
                'AddressLine1' => $data['AddressLine1'],
                'AddressLine2' => $data['AddressLine2'],
                'Street' => $data['Street'],
                'Area' => $data['Area'],
                'City' => $data['City'],
                'State' => $data['State'],
                'Country' => $data['Country'],
                'ZipCode' => $data['ZipCode'],
            ]);
            
            // if(array_key_exists('patientGroupmasterid', $data) && $data['patientGroupmasterid']) {
                $patient->patient_communication_group()->create([
                    'CommunicationGroupMasterGuid' => $data['patientGroupmasterid'] ?? null,
                    'GroupType' => $data['grouptype'] ?? null,
                    'GroupName' => $data['groupname'] ?? null,
                    'GroupDescription' => $data['groupdesc'] ?? null,
                    'IsDeleted' => false,
                    'CreatedOn' => now(),
                    'CreatedBy' => $data['CreatedBy'] ?? null,
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => $data['LastUpdatedBy'] ??null 
                ]);
            // }

            return $patient;
        });
    }

    public function getPatients($perPage = 50, $search = null, $dateFilter = 'all', $startDate, $endDate)
    {
        if ($dateFilter === 'all') {
            $patientList = Patient::when($search, function (Builder $query, string $search) {
                $query->whereAny(['FirstName', 'LastName'], 'like', "%$search%");
            })
                ->paginate($perPage);
        } else {
            // Use whereHas when you want to filter patients based on conditions in their related appointments.
            // Use with when you want to eager load appointments, but not filter patients by them.
            // Here, since you want to filter patients who have appointments matching certain criteria, whereHas is correct.
            $patientList = Patient::when($search, function (Builder $query, string $search) {
                $query->whereAny(['FirstName', 'LastName'], 'like', "%$search%");
            })
            ->where(function($query) use ($dateFilter, $startDate, $endDate) {
                // Include patients with matching appointments
                $query->whereHas('appointments', function (Builder $apptQuery) use ($dateFilter, $startDate, $endDate) {
                    if ($dateFilter === 'today') {
                        $apptQuery->whereDate('StartDateTime', Carbon::now()->toDateString());
                    } elseif ($dateFilter === 'recent') {
                        $apptQuery->whereDate('StartDateTime', '>=', Carbon::now()->subDays(7)->toDateString())
                            ->whereDate('StartDateTime', '<=', Carbon::now()->toDateString());
                    } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
                        $apptQuery->whereDate('StartDateTime', '>=', $startDate)
                            ->whereDate('EndDateTime', '<=', $endDate);
                    }
                });
                
                // Also include patients without any appointments
                $query->orDoesntHave('appointments');
            })
            ->paginate($perPage);
        }

        return [
            'patients' => $patientList,
            'pagination' => [
                'currentPage' => $patientList->currentPage(),
                'perPage' => $patientList->perPage(),
                'total' => $patientList->total(),
            ]
        ];
    }

    /**
     * Elasticsearch-powered patient search with appointment date filtering.
     * Falls back to database search if Elasticsearch is unavailable.
     */
    public function getPatientsNew($perPage = 50, $search = null, $dateFilter = 'all', $startDate = null, $endDate = null)
    {
        $page = request('page', 1);
        $client = Patient::getElasticsearchClient();
        // Fallback to database search if Elasticsearch is not available
        if (!$client) {
            return $this->getDatabasePatients($perPage, $search, $dateFilter, $startDate, $endDate);
        }

        try {
            return $this->getElasticsearchPatients($client, $perPage, $search, $dateFilter, $startDate, $endDate, $page);
        } catch (\Exception $e) {
            Log::warning('Elasticsearch query failed, falling back to database: ' . $e->getMessage());
            return $this->getDatabasePatients($perPage, $search, $dateFilter, $startDate, $endDate);
        }
    }

    /**
     * Get all patients without pagination.
     * Elasticsearch-powered patient search with appointment date filtering.
     * Falls back to database search if Elasticsearch is unavailable.
     */
    public function getAllPatients($search = null, $dateFilter = 'all', $startDate = null, $endDate = null)
    {
        $client = Patient::getElasticsearchClient();

        // Fallback to database search if Elasticsearch is not available
        if (!$client) {
            return $this->getAllDatabasePatients($search, $dateFilter, $startDate, $endDate);
        }

        try {
            return $this->getAllElasticsearchPatients($client, $search, $dateFilter, $startDate, $endDate);
        } catch (\Exception $e) {
            Log::warning('Elasticsearch query failed, falling back to database: ' . $e->getMessage());
            return $this->getAllDatabasePatients($search, $dateFilter, $startDate, $endDate);
        }
    }

    public function deletePatient($patient)
    {
        $patient->delete();
        return true;
    }

    /**
     * Update an existing patient record with address information.
     *
     * @param Patient $patient The patient model to update
     * @param array $data The validated data for updating the patient
     * @return Patient The updated patient model
     */
    public function updatePatientWithAddress(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            // Capture original patient data BEFORE updating
            $originalPatient = $patient->replicate();
            // Handle address update/creation
            // Helper function to check if a value should be used or fall back to existing
            $getValueOrFallback = function($newValue, $fallbackValue) {
                return (isset($newValue) && $newValue !== null && $newValue !== '') ? $newValue : $fallbackValue;
            };

            $addressData = [
                'AddressLine1' => $getValueOrFallback($data['AddressLine1'] ?? null, $originalPatient->AddressLine1),
                'AddressLine2' => $getValueOrFallback($data['AddressLine2'] ?? null, $originalPatient->AddressLine2),
                'Street' => $getValueOrFallback($data['Street'] ?? null, $originalPatient->Street),
                'Area' => $getValueOrFallback($data['Area'] ?? null, $originalPatient->Area),
                'City' => $getValueOrFallback($data['City'] ?? null, $originalPatient->City),
                'State' => $getValueOrFallback($data['State'] ?? null, $originalPatient->State),
                'Country' => $getValueOrFallback($data['Country'] ?? null, $originalPatient->Country),
                'ZipCode' => $getValueOrFallback($data['ZipCode'] ?? null, $originalPatient->ZipCode),
            ];

            // Check if patient address exists, if not create it
            $patientAddress = $patient->patient_addresses()->first();
            if ($patientAddress) {
                $patientAddress->update($addressData);
            } else {
                // Create new address record
                $addressData['PatientID'] = $patient->PatientID;
                $patient->patient_addresses()->create($addressData);
            }
            
            $data['AddressLine1']=$getValueOrFallback($data['AddressLine1'] ?? null, $originalPatient->AddressLine1);
            $data['AddressLine2']=$getValueOrFallback($data['AddressLine2'] ?? null, $originalPatient->AddressLine2);
            $data['Street']=$getValueOrFallback($data['Street'] ?? null, $originalPatient->Street);
            $data['Area']=$getValueOrFallback($data['Area'] ?? null, $originalPatient->Area);
            $data['City']=$getValueOrFallback($data['City'] ?? null, $originalPatient->City);
            $data['State']=$getValueOrFallback($data['State'] ?? null, $originalPatient->State);
            $data['Country']=$getValueOrFallback($data['Country'] ?? null, $originalPatient->Country);
            $data['ZipCode']=$getValueOrFallback($data['ZipCode'] ?? null, $originalPatient->ZipCode);
            $data['EmailAddress1'] = $getValueOrFallback($data['EmailAddress1'] ?? null, $originalPatient->EmailAddress1);
            
            if(array_key_exists('patientGroupmasterid', $data) && $data['patientGroupmasterid']) {
                $patientcommunicationData = [
                    'CommunicationGroupMasterGuid' => $data['patientGroupmasterid'] ?? null,
                    'GroupType' => $data['grouptype'] ?? null,
                    'GroupName' => $data['groupname'] ?? null,
                    'GroupDescription' => $data['groupdesc'] ?? null,
                    'IsDeleted' => false,
                    'CreatedOn' => now(),
                    'CreatedBy' => $data['CreatedBy'] ?? null,
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => $data['LastUpdatedBy'] ??null
                ];
    
                // Check if patient address exists, if not create it
                $patientcommunication = $patient->patient_communication_group()->first();
                if ($patientcommunication) {
                    $patientcommunication->update($patientcommunicationData);
                } else {
                    // Create new address record
                    $patientcommunicationData['PatientID'] = $patient->PatientID;
                    $patient->patient_communication_group()->create($patientcommunicationData);
                }
            }
            
                        // Remove PatientID from update data to prevent it from being changed
            unset($data['PatientID']);
            
            // Update patient with filtered data
            $patient->fill($data);
            $patient->save();
            $building = Building::where('building_name', $data['Building'] ?? '')
                ->where('address1', $data['AddressLine1'] ?? '')
                ->where('city', $data['City'] ?? '')
                ->where('state', $data['State'] ?? '')
                ->where('country', $data['Country'] ?? '')
                ->where('pincode', $data['ZipCode'] ?? '')
                ->where('IsDeleted', false)
                ->first();
                if($building && $building->id != $patient->building_id){
                    $patient->building_id = $building->id;
                    $patient->save();
                }

            if (!$building && $data['Building']) {
                $building = Building::create([
                    'building_name' => $data['Building'] ?? null,
                    'building_code' => $data['building_code'] ?? null,
                    'address1' => $data['AddressLine1'] ?? null,
                    'address2' => $data['AddressLine2'] ?? null,
                    'area' => $data['Area'] ?? null,
                    'city' => $data['City'] ?? null,
                    'state' => $data['State'] ?? null,
                    'country' => $data['Country'] ?? null,
                    'pincode' => $data['ZipCode'] ?? null,
                    'status' => true,
                    'IsDeleted' => false,
                    'CreatedOn' => now(),
                    'CreatedBy' => $data['CreatedBy'] ?? null,
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => $data['LastUpdatedBy'] ??null 
                ]);
            }
            
            // Only update building_id if we have a valid building
            if ($building) {
                $patient->building_id = $building->id;
                $patient->save();
            }
            
            // Return the updated patient with all relationships loaded
            return $patient->fresh();
        });
    }

    /**
     * Store the photo path for a patient.
     *
     * @param Patient $patient The patient model to update
     * @param string $photoPath The path to the uploaded photo
     * @return Patient The updated patient model
     */
    public function storePatientPhoto(Patient $patient, string $photoPath): Patient
    {
        $patient->ImagePath = $photoPath;
        $patient->save();
        return $patient;
    }

    /**
     * Store the photo path for a patient.
     *
     * @param Patient $patient The patient model to update
     * @param string $photoPath The path to the uploaded photo
     * @return Patient The updated patient model
     */
    public function storePatientSignature(Patient $patient, string $photoPath, $SignatureDate): Patient
    {
        $patient->Signatures = $photoPath;
        $patient->SignatureDate = $SignatureDate;
        $patient->save();
        return $patient;
    }

    /**
     * Get patient profile photo by patient ID.
     *
     * @param int|string $patientId The patient ID
     * @return array|null Returns array with patient ID and photo URL, or null if not found
     */
    public function getPatientProfilePhoto($patient)
    {
        // Fetch from patientcopy table
        // $patient = DB::table('patientcopy')->where('PatientID', $patient->id)->first();

        // if (!$patient || !$patient->ImagePath) {
        //     return null;
        // }

        // Build full URL using Laravel's asset() helper
        $photoUrl = asset($patient->ImagePath);

        return $photoUrl;
    }

    public function getPatientWithAppointments($patient)
    {
        try {
            return new PatientWithAppointmentResource($patient);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Patient not found.');
        } catch (Exception $e) {
            throw new Exception('An error occurred while retrieving the patient data.');
        }
    }

    /**
     * Advanced search based on JSON filters (no Elasticsearch dependency).
     * Supported keys: first_name, last_name, mobile, building.
     * "building" maps to AddressLine1 (adjust if different schema).
     */
    public function advancedSearchPatients(int $perPage = 50, array $filters = [])
    {
        $query = Patient::query()->with('family');
;        // Search by first name
        if (!empty($filters['first_name'])) {
            $query->where('FirstName', 'like', '%' . $filters['first_name'] . '%');
        }

        // Search by last name
        if (!empty($filters['last_name'])) {
            $query->where('LastName', 'like', '%' . $filters['last_name'] . '%');
        }

        // Search by mobile number (mobile or phone)
        if (!empty($filters['mobile'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('MobileNumber', 'like', '%' . $filters['mobile'] . '%')
                ->orWhere('PhoneNumber', 'like', '%' . $filters['mobile'] . '%');
            });
        }

        // Search by building name (AddressLine1)
        if (!empty($filters['building'])) {
            $query->where('AddressLine1', 'like', '%' . $filters['building'] . '%');
        }
        
        // Search by area
        if (!empty($filters['area'])) {
            $query->where('Area', 'like', '%' . $filters['area'] . '%');
        }

        // Search by family name (from Family table)
        if (!empty($filters['family'])) {
            $query->orWhereHas('family', function ($q) use ($filters) {
                $q->where('FamilyName', 'like', '%' . $filters['family'] . '%');
            });
        }
        
        // Search by family code (from Family table)
        if (!empty($filters['family_group'])) {
            $query->orWhereHas('family', function ($q) use ($filters) {
                $q->where('FamilyCode',$filters['family_group']);
            });
        }

        // Search by referred by
        if (!empty($filters['referred'])) {
            $query->orWhere('ReferredBy', $filters['referred']);
        }

        // Search by patient notes
        if (!empty($filters['notes'])) {
            $query->orWhere('PatientNotes',$filters['notes'] );
        }

        // Search by profession (occupation)
        if (!empty($filters['profession'])) {
            $query->orWhere('Occupation',$filters['profession']);
        }

        // Search by pincode (patient code)
        if (!empty($filters['pincode'])) {
            $query->orWhere('PatientCode',$filters['pincode']);
        }

        // Optional: Search by religion if column exists
        if (!empty($filters['religion'])) {
            if (Schema::hasColumn('Patient', 'Religion')) {
                $query->orWhere('Religion', $filters['religion']);
            }
        }

        // Optional: Search by caste/cast if column exists (handle common variants)
        if (!empty($filters['cast'])) {
            if (Schema::hasColumn('Patient', 'Caste')) {
                $query->orWhere('Caste',$filters['cast'] );
            } elseif (Schema::hasColumn('Patient', 'Cast')) {
                $query->orWhere('Cast', $filters['cast']);
            }
        }

        // Paginate results
        $patientList = $query->paginate($perPage);

        return [
            'patients' => $patientList,
            'pagination' => [
                'currentPage' => $patientList->currentPage(),
                'perPage' => $patientList->perPage(),
                'total' => $patientList->total(),
            ]
        ];
    }


    /**
     * Database fallback for patient search with appointment date filtering.
     */
    private function getDatabasePatients($perPage = 50, $search = null, $dateFilter = 'all', $startDate = null, $endDate = null)
    {
        $page = request('page', 1);
        
        // Initialize search variables outside conditions
        $searchTerms = null;
        $fullName = null;
        
        if ($search) {
            $searchTerms = array_filter(explode(' ', trim($search)));
            $fullName = trim($search);
        }
        
        if ($dateFilter === 'all') {
            $query = Patient::query();
            
            if ($search) {
                $query->where(function ($q) use ($searchTerms, $fullName, $search) {
                    if (count($searchTerms) >= 2) {
                        // Multiple words - treat as first and last name
                        $firstName = $searchTerms[0];
                        $lastName = $searchTerms[1];
                        
                        // Priority 1: Exact match on concatenated full name (case insensitive)
                        $q->whereRaw("LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) = LOWER(?)", [$fullName]);
                        
                        // Priority 2: FirstName matches first term AND LastName starts with second term
                        $q->orWhere(function ($subQ) use ($firstName, $lastName) {
                            $subQ->whereRaw("LOWER(FirstName) LIKE LOWER(?)", [$firstName . '%'])
                                ->whereRaw("LOWER(LastName) LIKE LOWER(?)", [$lastName . '%']);
                        });
                        
                        // Priority 3: FirstName matches first term AND LastName contains second term (for multi-word last names like "JIGAR VORA")
                        $q->orWhere(function ($subQ) use ($firstName, $lastName) {
                            $subQ->whereRaw("LOWER(FirstName) LIKE LOWER(?)", [$firstName . '%'])
                                ->whereRaw("LOWER(LastName) LIKE LOWER(?)", ['%' . $lastName . '%']);
                        });
                        
                        // Priority 4: Try reverse order (LastName FirstName) - starts with
                        $q->orWhere(function ($subQ) use ($firstName, $lastName) {
                            $subQ->whereRaw("LOWER(FirstName) LIKE LOWER(?)", [$lastName . '%'])
                                ->whereRaw("LOWER(LastName) LIKE LOWER(?)", [$firstName . '%']);
                        });
                        
                        // Priority 5: Partial match in full name
                        $q->orWhereRaw("LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) LIKE LOWER(?)", ['%' . $fullName . '%']);
                        
                        // Priority 6: Either first or last name matches any term
                        foreach ($searchTerms as $term) {
                            $q->orWhereRaw("LOWER(FirstName) LIKE LOWER(?)", ['%' . $term . '%'])
                              ->orWhereRaw("LOWER(LastName) LIKE LOWER(?)", ['%' . $term . '%']);
                        }
                    } else {
                        // Single word search
                        $q->whereRaw("LOWER(FirstName) LIKE LOWER(?)", ['%' . $search . '%'])
                          ->orWhereRaw("LOWER(LastName) LIKE LOWER(?)", ['%' . $search . '%']);
                    }
                    
                    // Also search in other fields
                    $q->orWhere('EmailAddress1', 'like', '%' . $search . '%')
                      ->orWhere('EmailAddress2', 'like', '%' . $search . '%')
                      ->orWhere('PhoneNumber', 'like', '%' . $search . '%')
                      ->orWhere('MobileNumber', 'like', '%' . $search . '%');
                });
            }

            // Apply ordering
            if ($search && $fullName && $searchTerms && count($searchTerms) >= 2) {
                $firstName = $searchTerms[0];
                $lastName = $searchTerms[1];
                
                $query->orderByRaw("
                    CASE 
                        WHEN LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) = LOWER(?) THEN 1
                        WHEN LOWER(FirstName) LIKE LOWER(?) AND LOWER(LastName) LIKE LOWER(?) THEN 2
                        WHEN LOWER(FirstName) LIKE LOWER(?) AND LOWER(LastName) LIKE LOWER(?) THEN 3
                        WHEN LOWER(FirstName) LIKE LOWER(?) AND LOWER(LastName) LIKE LOWER(?) THEN 4
                        WHEN LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) LIKE LOWER(?) THEN 5
                        WHEN LOWER(FirstName) LIKE LOWER(?) OR LOWER(LastName) LIKE LOWER(?) THEN 6
                        ELSE 7
                    END,
                    AddedOn DESC
                ", [
                    $fullName,
                    $firstName . '%', $lastName . '%',
                    $firstName . '%', '%' . $lastName . '%',
                    $lastName . '%', $firstName . '%',
                    '%' . $fullName . '%',
                    '%' . $firstName . '%', '%' . $lastName . '%'
                ]);
            } else {
                $query->orderBy('AddedOn', 'desc');
            }

            $patientList = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $query = Patient::query();
            
            if ($search) {
                $query->where(function ($q) use ($searchTerms, $fullName, $search) {
                    if (count($searchTerms) >= 2) {
                        // Multiple words - treat as first and last name
                        $firstName = $searchTerms[0];
                        $lastName = $searchTerms[1];
                        
                        // Priority 1: Exact match on concatenated full name (case insensitive)
                        $q->whereRaw("LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) = LOWER(?)", [$fullName]);
                        
                        // Priority 2: FirstName matches first term AND LastName starts with second term
                        $q->orWhere(function ($subQ) use ($firstName, $lastName) {
                            $subQ->whereRaw("LOWER(FirstName) LIKE LOWER(?)", [$firstName . '%'])
                                ->whereRaw("LOWER(LastName) LIKE LOWER(?)", [$lastName . '%']);
                        });
                        
                        // Priority 3: FirstName matches first term AND LastName contains second term (for multi-word last names like "JIGAR VORA")
                        $q->orWhere(function ($subQ) use ($firstName, $lastName) {
                            $subQ->whereRaw("LOWER(FirstName) LIKE LOWER(?)", [$firstName . '%'])
                                ->whereRaw("LOWER(LastName) LIKE LOWER(?)", ['%' . $lastName . '%']);
                        });
                        
                        // Priority 4: Try reverse order (LastName FirstName) - starts with
                        $q->orWhere(function ($subQ) use ($firstName, $lastName) {
                            $subQ->whereRaw("LOWER(FirstName) LIKE LOWER(?)", [$lastName . '%'])
                                ->whereRaw("LOWER(LastName) LIKE LOWER(?)", [$firstName . '%']);
                        });
                        
                        // Priority 5: Partial match in full name
                        $q->orWhereRaw("LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) LIKE LOWER(?)", ['%' . $fullName . '%']);
                        
                        // Priority 6: Either first or last name matches any term
                        foreach ($searchTerms as $term) {
                            $q->orWhereRaw("LOWER(FirstName) LIKE LOWER(?)", ['%' . $term . '%'])
                              ->orWhereRaw("LOWER(LastName) LIKE LOWER(?)", ['%' . $term . '%']);
                        }
                    } else {
                        // Single word search
                        $q->whereRaw("LOWER(FirstName) LIKE LOWER(?)", ['%' . $search . '%'])
                          ->orWhereRaw("LOWER(LastName) LIKE LOWER(?)", ['%' . $search . '%']);
                    }
                    
                    // Also search in other fields
                    $q->orWhere('EmailAddress1', 'like', '%' . $search . '%')
                      ->orWhere('EmailAddress2', 'like', '%' . $search . '%')
                      ->orWhere('PhoneNumber', 'like', '%' . $search . '%')
                      ->orWhere('MobileNumber', 'like', '%' . $search . '%');
                });
            }

            // Apply date filtering
            $query->where(function($q) use ($dateFilter, $startDate, $endDate) {
                $q->whereHas('appointments', function (Builder $apptQuery) use ($dateFilter, $startDate, $endDate) {
                    if ($dateFilter === 'today') {
                        $apptQuery->whereDate('StartDateTime', Carbon::now()->toDateString());
                    } elseif ($dateFilter === 'recent') {
                        $apptQuery->whereDate('StartDateTime', '>=', Carbon::now()->subDays(7)->toDateString())
                                ->whereDate('StartDateTime', '<=', Carbon::now()->toDateString());
                    } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
                        $apptQuery->whereDate('StartDateTime', '>=', $startDate)
                                ->whereDate('EndDateTime', '<=', $endDate);
                    }
                })
                ->orDoesntHave('appointments');
            });

            // Apply ordering
            if ($search && $fullName && $searchTerms && count($searchTerms) >= 2) {
                $firstName = $searchTerms[0];
                $lastName = $searchTerms[1];
                
                $query->orderByRaw("
                    CASE 
                        WHEN LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) = LOWER(?) THEN 1
                        WHEN LOWER(FirstName) LIKE LOWER(?) AND LOWER(LastName) LIKE LOWER(?) THEN 2
                        WHEN LOWER(FirstName) LIKE LOWER(?) AND LOWER(LastName) LIKE LOWER(?) THEN 3
                        WHEN LOWER(FirstName) LIKE LOWER(?) AND LOWER(LastName) LIKE LOWER(?) THEN 4
                        WHEN LOWER(CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, ''))) LIKE LOWER(?) THEN 5
                        WHEN LOWER(FirstName) LIKE LOWER(?) OR LOWER(LastName) LIKE LOWER(?) THEN 6
                        ELSE 7
                    END,
                    AddedOn DESC
                ", [
                    $fullName,
                    $firstName . '%', $lastName . '%',
                    $firstName . '%', '%' . $lastName . '%',
                    $lastName . '%', $firstName . '%',
                    '%' . $fullName . '%',
                    '%' . $firstName . '%', '%' . $lastName . '%'
                ]);
            } else {
                $query->orderBy('AddedOn', 'desc');
            }

            $patientList = $query->paginate($perPage, ['*'], 'page', $page);
        }

        return [
            'patients' => $patientList,
            'pagination' => [
                'current_page' => $patientList->currentPage(),
                'per_page' => $patientList->perPage(),
                'total' => $patientList->total(),
            ]
        ];
    }

    /**
     * Elasticsearch search for patients with appointment date filtering.
     */
    private function getElasticsearchPatients($client, $perPage, $search, $dateFilter, $startDate, $endDate, $page)
    {
        $must = [];

        if ($search) {
            // Check if search contains multiple words (likely first and last name)
            $searchTerms = explode(' ', trim($search));
            if (count($searchTerms) >= 2) {
                // Multiple words - likely first and last name
                $must[] = [
                    'bool' => [
                        'should' => [
                            // Full name as phrase in concatenated field
                            [
                                'match_phrase' => [
                                    'full_name' => $search
                                ]
                            ],
                            // Cross-field search for full name (exact phrase)
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => ['first_name', 'last_name'],
                                    'type' => 'cross_fields',
                                    'operator' => 'and'
                                ]
                            ],
                            // Phrase search across name fields with boosting
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^3',
                                        'last_name^3'
                                    ],
                                    'type' => 'phrase_prefix'
                                ]
                            ],
                            // Individual terms search
                            [
                                'bool' => [
                                    'should' => array_map(function($term) {
                                        return [
                                            'multi_match' => [
                                                'query' => trim($term),
                                                'fields' => ['first_name^2', 'last_name^2'],
                                                'type' => 'best_fields'
                                            ]
                                        ];
                                    }, array_filter($searchTerms, function($term) {
                                        return !empty(trim($term));
                                    }))
                                ]
                            ],
                            // Fuzzy search for partial matches
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^2',
                                        'last_name^2',
                                        'email',
                                        'email2',
                                        'phone',
                                        'mobile'
                                    ],
                                    'fuzziness' => 'AUTO',
                                    'prefix_length' => 1,
                                    'operator' => 'OR'
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1
                    ]
                ];
            } else {
                // Single word search - use original logic
                $must[] = [
                    'bool' => [
                        'should' => [
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^3',
                                        'last_name^3',
                                        'first_name',
                                        'last_name',
                                        'email',
                                        'email2',
                                        'phone',
                                        'mobile'
                                    ],
                                    'type' => 'phrase_prefix'
                                ]
                            ],
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^2',
                                        'last_name^2',
                                        'first_name',
                                        'last_name',
                                        'email',
                                        'email2',
                                        'phone',
                                        'mobile'
                                    ],
                                    'fuzziness' => 'AUTO',
                                    'prefix_length' => 1,
                                    'operator' => 'OR'
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1
                    ]
                ];
            }
        }
        // Appointment date filter logic
        if ($dateFilter !== 'all') {
            $dateRange = [];
            if ($dateFilter === 'today') {
                $date = Carbon::now()->toDateString();
                $dateRange = ['gte' => $date, 'lte' => $date];
            } elseif ($dateFilter === 'recent') {
                $dateRange = [
                    'gte' => Carbon::now()->subDays(7)->toDateString(),
                    'lte' => Carbon::now()->toDateString(),
                ];
            } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
                $dateRange = ['gte' => $startDate, 'lte' => $endDate];
            }
            if ($dateRange) {
                $must[] = [
                    'range' => [
                        'appointments.start_date' => $dateRange
                    ]
                ];
            }
        }
        $params = [
            'index' => 'patients',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $must
                    ]
                ],
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'sort' => [
                    [
                        'id' => [
                            'order' => 'desc'
                        ]
                    ]
                ]
            ]
        ];
             
        $results = $client->search($params);
        $hits = $results['hits']['hits'] ?? [];
   
        $ids = collect($hits)->pluck('_source.id')->unique()->all();
        $patients = Patient::whereIn('PatientID', $ids)
                    ->orderBy('AddedOn', 'desc') // Sort by latest records first
                    ->get();

        // Manual pagination
        $total = $results['hits']['total']['value'] ?? 0;

        return [
            'patients' => $patients,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
            ]
        ];
    }

    /**
     * Database fallback for getting all patients without pagination.
     */
    private function getAllDatabasePatients($search = null, $dateFilter = 'all', $startDate = null, $endDate = null)
    {
        if ($dateFilter === 'all') {
            $patients = Patient::when($search, function (Builder $query, string $search) {
                $query->where(function ($q) use ($search) {
                    // Check if search contains multiple words (likely first and last name)
                    $searchTerms = explode(' ', trim($search));

                    if (count($searchTerms) >= 2) {
                        // Multiple words - likely first and last name
                        // Use CONCAT with COALESCE to handle null values and support both orders
                        $q->where(function ($subQ) use ($search, $searchTerms) {
                            // Try full name as 'FirstName LastName'
                            $subQ->whereRaw("CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, '')) LIKE ?", ['%' . $search . '%']);
                        });
                    } else {
                        // Single word search
                        $q->where('FirstName', 'like', '%' . $search . '%')
                          ->orWhere('LastName', 'like', '%' . $search . '%');
                    }
                    
                    // Also search in other fields
                    $q->orWhere('EmailAddress1', 'like', '%' . $search . '%')
                      ->orWhere('EmailAddress2', 'like', '%' . $search . '%')
                      ->orWhere('PhoneNumber', 'like', '%' . $search . '%')
                      ->orWhere('MobileNumber', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('AddedOn', 'desc') // Sort by latest records first
            ->get();
                
        } else {
            $patients = Patient::when($search, function (Builder $query, string $search) {
                    $query->where(function ($q) use ($search) {
                        // Check if search contains multiple words (likely first and last name)
                        $searchTerms = explode(' ', trim($search));

                        if (count($searchTerms) >= 2) {
                            // Multiple words - likely first and last name
                            // Use CONCAT with COALESCE to handle null values and support both orders
                            $q->where(function ($subQ) use ($search, $searchTerms) {
                                // Try full name as 'FirstName LastName'
                                $subQ->whereRaw("CONCAT(COALESCE(FirstName, ''), ' ', COALESCE(LastName, '')) LIKE ?", ['%' . $search . '%']);
                            });
                        } else {
                            // Single word search
                            $q->where('FirstName', 'like', '%' . $search . '%')
                              ->orWhere('LastName', 'like', '%' . $search . '%');
                        }

                        // Also search in other fields
                        $q->orWhere('EmailAddress1', 'like', '%' . $search . '%')
                          ->orWhere('EmailAddress2', 'like', '%' . $search . '%')
                          ->orWhere('PhoneNumber', 'like', '%' . $search . '%')
                          ->orWhere('MobileNumber', 'like', '%' . $search . '%');
                    });
                })
                ->whereHas('appointments', function (Builder $query) use ($dateFilter, $startDate, $endDate) {
                    if ($dateFilter === 'today') {
                        $query->whereDate('StartDateTime', Carbon::now()->toDateString());
                    } elseif ($dateFilter === 'recent') {
                        $query->whereDate('StartDateTime', '>=', Carbon::now()->subDays(7)->toDateString())
                            ->whereDate('StartDateTime', '<=', Carbon::now()->toDateString());
                    } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
                        $query->whereDate('StartDateTime', '>=', $startDate)
                            ->whereDate('EndDateTime', '<=', $endDate);
                    }
                })
                ->orderBy('AddedOn', 'desc') // Sort by latest records first
                ->get();
        }

        return [
            'patients' => $patients,
            'total' => $patients->count()
        ];
    }

    /**
     * Elasticsearch search for all patients without pagination.
     */
    private function getAllElasticsearchPatients($client, $search, $dateFilter, $startDate, $endDate)
    {
        $must = [];

        if ($search) {
            // Check if search contains multiple words (likely first and last name)
            $searchTerms = explode(' ', trim($search));
            
            if (count($searchTerms) >= 2) {
                // Multiple words - likely first and last name
                $must[] = [
                    'bool' => [
                        'should' => [
                            // Full name as phrase in concatenated field
                            [
                                'match_phrase' => [
                                    'full_name' => $search
                                ]
                            ],
                            // Cross-field search for full name (exact phrase)
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => ['first_name', 'last_name'],
                                    'type' => 'cross_fields',
                                    'operator' => 'and'
                                ]
                            ],
                            // Phrase search across name fields with boosting
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^3',
                                        'last_name^3'
                                    ],
                                    'type' => 'phrase_prefix'
                                ]
                            ],
                            // Individual terms search
                            [
                                'bool' => [
                                    'should' => array_map(function($term) {
                                        return [
                                            'multi_match' => [
                                                'query' => trim($term),
                                                'fields' => ['first_name^2', 'last_name^2'],
                                                'type' => 'best_fields'
                                            ]
                                        ];
                                    }, array_filter($searchTerms, function($term) {
                                        return !empty(trim($term));
                                    }))
                                ]
                            ],
                            // Fuzzy search for partial matches
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^2',
                                        'last_name^2',
                                        'email',
                                        'email2',
                                        'phone',
                                        'mobile'
                                    ],
                                    'fuzziness' => 'AUTO',
                                    'prefix_length' => 1,
                                    'operator' => 'OR'
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1
                    ]
                ];
            } else {
                // Single word search - use original logic
                $must[] = [
                    'bool' => [
                        'should' => [
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^3',
                                        'last_name^3',
                                        'first_name',
                                        'last_name',
                                        'email',
                                        'email2',
                                        'phone',
                                        'mobile'
                                    ],
                                    'type' => 'phrase_prefix'
                                ]
                            ],
                            [
                                'multi_match' => [
                                    'query' => $search,
                                    'fields' => [
                                        'first_name^2',
                                        'last_name^2',
                                        'first_name',
                                        'last_name',
                                        'email',
                                        'email2',
                                        'phone',
                                        'mobile'
                                    ],
                                    'fuzziness' => 'AUTO',
                                    'prefix_length' => 1,
                                    'operator' => 'OR'
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1
                    ]
                ];
            }
        }

        // Appointment date filter logic
        if ($dateFilter !== 'all') {
            $dateRange = [];
            if ($dateFilter === 'today') {
                $date = Carbon::now()->toDateString();
                $dateRange = ['gte' => $date, 'lte' => $date];
            } elseif ($dateFilter === 'recent') {
                $dateRange = [
                    'gte' => Carbon::now()->subDays(7)->toDateString(),
                    'lte' => Carbon::now()->toDateString(),
                ];
            } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
                $dateRange = ['gte' => $startDate, 'lte' => $endDate];
            }
            if ($dateRange) {
                $must[] = [
                    'range' => [
                        'appointments.start_date' => $dateRange
                    ]
                ];
            }
        }

        // Use a large size to get all results or use scroll API for very large datasets
        $params = [
            'index' => 'patients',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $must
                    ]
                ],
                'size' => 10000,  // Set a high limit to get all results
                'sort' => [
                    [
                        'id' => [
                            'order' => 'desc'
                        ]
                    ]
                ]
            ]
        ];
        
        $results = $client->search($params);
        $hits = $results['hits']['hits'] ?? [];
        $ids = collect($hits)->pluck('_source.id')->unique()->all();
        $patients = Patient::whereIn('PatientID', $ids)
                    ->orderBy('AddedOn', 'desc') // Sort by latest records first
                    ->get();

        $total = $results['hits']['total']['value'] ?? 0;

        return [
            'patients' => $patients,
            'total' => $total
        ];
    }
}
