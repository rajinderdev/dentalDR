<?php

namespace App\Services;

use App\Http\Resources\WaitingAreaPatientResource;
use App\Models\Patient;
use App\Models\WaitingAreaPatient;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PatientTreatmentsDone;
use App\Models\Provider;

class WaitingAreaPatientService
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }
    public function getWaitingAreaForToday($doctorIds = [], $providerId = null)
    {
        $today = Carbon::today()->toDateString();

        $user = Auth::user();
        $isDoctor = $user && $user->role && strtolower($user->role->RoleName) === 'doctor';

        // For doctor users: ALWAYS enforce their own ProviderID (ignore request filters).
        if ($isDoctor) {
            $providerId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            $doctorIds = [];
        } else {
            // For admin/staff: UI might send ProviderIDs or UserIDs; normalize to ProviderIDs.
            if (!empty($doctorIds)) {
                $doctorIds = is_array($doctorIds) ? $doctorIds : [$doctorIds];
                $doctorIds = array_values(array_filter(array_map('strval', $doctorIds), fn ($v) => trim($v) !== ''));

                $providerIdsByProviderId = Provider::whereIn('ProviderID', $doctorIds)->pluck('ProviderID')->all();
                $providerIdsByUserId = Provider::whereIn('UserID', $doctorIds)->pluck('ProviderID')->all();
                $doctorIds = array_values(array_unique(array_merge($providerIdsByProviderId, $providerIdsByUserId)));
            }

            if (!empty($providerId)) {
                $providerId = (string) $providerId;
                $normalizedProviderId = Provider::where('ProviderID', $providerId)->value('ProviderID');
                if (!$normalizedProviderId) {
                    $normalizedProviderId = Provider::where('UserID', $providerId)->value('ProviderID');
                }
                $providerId = $normalizedProviderId ?: $providerId;
            }
        }
        
        // Get WaitingAreaIDs from PatientTreatmentsDone with WaitingAreaFlag = 1
        $waitingAreaIds = PatientTreatmentsDone::query()
            ->when(!empty($doctorIds), function ($query) use ($doctorIds) {
                $query->whereIn('ProviderID', $doctorIds);
            })
            ->when($providerId, function ($query) use ($providerId) {
                $query->where('ProviderID', $providerId);
            })
            ->whereDate('TreatmentDate', $today)
            ->where('IsCompleted', 0)
            ->where('WaitingAreaFlag', 1)
            ->where('ParentPatientTreatmentDoneID', '00000000-0000-0000-0000-000000000000')
            ->pluck('WaitingAreaID')
            ->filter()
            ->unique() 
            ->values();

        // Get waiting area patients
        $appointments = WaitingAreaPatient::query()
            ->when(!empty($doctorIds), function ($query) use ($doctorIds) {
                $query->whereIn('ProviderID', $doctorIds);
            })
            ->when($providerId, function ($query) use ($providerId) {
                $query->where('ProviderID', $providerId);
            })
            ->where(function ($query) use ($today, $waitingAreaIds) {
                $query
                    ->whereDate('StartDateTime', $today)
                    ->where('MovedToTreatmentArea', 0)
                    ->where('IsDeleted', 0);

                if ($waitingAreaIds->isNotEmpty()) {
                    $query->orWhereIn('WaitingAreaID', $waitingAreaIds);
                }
            })
            ->orderBy('CreatedOn', 'desc')
            ->get();

        return [
            'count' => $appointments->count(), 
            'list' => WaitingAreaPatientResource::collection($appointments)
        ];
    }

    public function getWaitingAreaList()
    {
        $appointments = WaitingAreaPatient::with('provider:ProviderID,ProviderName,Location,Email')
        ->select(['WaitingAreaID', 'PatientID', 'StartDateTime', 'EndDateTime', 'PatientName', 'Status', 'CompleteTime', 'ProviderID'])
        ->get();

        return WaitingAreaPatientResource::collection($appointments);
    }

    public function createWaitingAreaPatient(array $data): WaitingAreaPatient
    {
        $user = Auth::user();
        return DB::transaction(function() use ($data, $user) {
            if(!$data['IsExistingPatient']) {
                $lastRecord = Patient::orderBy('CaseID', 'desc')->first();
                $patient = Patient::create([
                    'ProviderID' => $data['ProviderID'],
                    'ClinicID' => $user->ClinicID,
                    'Title' => $data['Title'],
                    'FirstName' => $data['FirstName'],
                    'LastName' => $data['LastName'],
                    'Gender' => $data['Gender'],
                    'Age' => $data['Age'],
                    'Nationality' => $data['Nationality'],
                    'MobileNumber' => $data['Mobile'],
                    'CaseID' => $lastRecord ? $lastRecord->CaseID + 1 : 1,
                    'PatientCode' => $lastRecord ? 'P' . $lastRecord->CaseID + 1 : 'P1',
                    'AddressLine1' => '',
                    'City' => '',
                    'State' => '',
                    'Country' => 0,
                ]);
            } else {
                $patient = Patient::where('PatientID', $data['PatientID'])->first();
                
                if (!$patient) {
                    throw new Exception('Patient not found');
                }
            }

            $data['PatientID'] = $patient->PatientID;
            $data['PatientName'] = trim($patient->Title . ' ' . $patient->FirstName . ' ' . $patient->LastName);
            $data['Mobile'] = $patient->MobileNumber;
            $data['ArrivalTime'] = $data['ArrivalTime'] ?? Carbon::now();
            $lastRecord = WaitingAreaPatient::orderBy('TokenNumber', 'desc')->first();
            $waitingArea = WaitingAreaPatient::create([
                'ClinicID' => $user->ClinicID,
                'PatientID' => $data['PatientID'],
                'AppointmentID' => $data['AppointmentID'] ?? null,
                'ProviderID' => $data['ProviderID'],
                'PatientName' => $data['PatientName'],
                'StartDateTime' => $data['StartDateTime'] ?? $data['ArrivalTime'],
                'EndDateTime' => $data['EndDateTime'] ?? null,
                'Comments' => $data['Comments'] ?? null,
                'ReminderDate' => $data['ReminderDate'] ?? null,
                'CancelledOn' => $data['CancelledOn'] ?? null,
                'CancelledBy' => $data['CancelledBy'] ?? null,
                'CancellationReason' => $data['CancellationReason'] ?? null,
                'CancellationType' => $data['CancellationType'] ?? null,
                'ArrivalTime' => $data['ArrivalTime'] ?? null,
                'OperationTime' => $data['OperationTime'] ?? null,
                'CompleteTime' => $data['CompleteTime'] ?? null,
                'PatientPhone' => $data['Mobile'] ?? null,
                'Status' => $data['Status'] ?? 'Waiting',
                'TokenNumber' => $lastRecord ? $lastRecord->TokenNumber + 1 : 1,
                'IsDeleted' => $data['IsDeleted'] ?? 0,
                'WaitTime' => $data['WaitTime'] ?? null,
                'ChairID' => $data['ChairID'] ?? null,
                'CreatedOn' => Carbon::now(),
                'CreatedBy' => $data['CreatedBy'] ?? 'System',
                'LastUpdatedOn' => Carbon::now(),
                'LastUpdatedBy' => $data['UpdatedBy'] ?? 'System',
            ]);

            return $waitingArea;
        });
    }

    public function updateWaitingAreaPatient(WaitingAreaPatient $request, array $data): WaitingAreaPatient
    {
        
        $request->update($data);
        if(array_key_exists('IsDeleted',$data) && $data['IsDeleted']===true){
            if($request->AppointmentID) {
                $appointment = \App\Models\Appointment::find($request->AppointmentID);
                
                if($appointment) {
                    $patient = Patient::find($request->PatientID);
                    
                    if($patient) {
                        $appointmentService = $this->appointmentService;
                        $appointmentData = [
                            'IsExistingPatient' => true,
                            'PatientID' => $request->PatientID,
                            'ProviderID' => $request->ProviderID ?? $appointment->ProviderID,
                            'AppointmentID' => $request->AppointmentID, 
                            'Status' => 'Rescheduled', 
                            'StartDateTime' => $request['StartDateTime'] ?? Carbon::now()->addDay()->toDateTimeString(),
                            'EndDateTime' => $request['EndDateTime'] ?? Carbon::now()->addDay()->addHour()->toDateTimeString(),
                            'Comments' => $request['Comments'] ?? 'Rescheduled from waiting area',
                            'RescheduleReason' => $request['RescheduleReason'] ?? NULL,
                        ];
                        $appointmentService->createAppointment(null, $appointmentData);
                    }
                }
            }
        }
        $request->fresh();
        return $request;
    }
}
