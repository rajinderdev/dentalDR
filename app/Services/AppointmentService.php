<?php

namespace App\Services;

use App\Http\Resources\AppointmentResource;
use App\Http\Resources\TreatmentResource;
use App\Models\Appointment;
use App\Models\AppointmentRescheduled;
use App\Models\Patient;
use Exception;
use App\Helpers\EntityDataHelper;
use App\Models\PatientTreatmentsDone;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WaitingAreaPatient;

class AppointmentService
{
    protected $providerSlotService;

    public function __construct(ProviderSlotService $providerSlotService)
    {
        $this->providerSlotService = $providerSlotService;
    }
    public function getAppointments($patientId, int $perPage)
    {
        $appointments = Appointment::when($patientId, function ($query) use ($patientId) {
            $query->where('PatientID', $patientId);
        })->orderBy('CreatedOn', 'desc')->paginate($perPage);
        return [
            'appointments' => $appointments, // Transform the data using the resource
            'pagination' => [
                'current_page' => $appointments->currentPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
                'last_page' => $appointments->lastPage(),
            ]
        ];
    }

    public function getAppointmentsForToday($doctorIds = [], $providerId = null)
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

        $appointments = Appointment::query()
            ->when(!empty($doctorIds), fn ($query) => $query->whereIn('ProviderID', $doctorIds))
            ->when(!empty($providerId), fn ($query) => $query->where('ProviderID', $providerId));

        $appointments = $appointments->whereDate('StartDateTime', $today)->where('MovedToWaitingArea', 0)->whereNull('ArrivalTime')->where('Status','Scheduled')
        ->orderBy('CreatedOn', 'desc') // Sort by latest records first
        ->get();

        $appointmentsCount = $appointments->count();

        return ['count' => $appointmentsCount, 'list' => AppointmentResource::collection($appointments)];
    }

    public function getAppointmentsByStatus()
    {
        $appointments = Appointment::where('Status', 'Scheduled')->orderBy('CreatedOn', 'desc')
        ->get();

        return $appointments;
    }

    public function getTreatment($status)
    {
        // Eager-load the provider relationship, selecting only specific columns from provider
        $treatments = Appointment::where('Status', '=', $status)->orderBy('CreatedOn', 'desc')->get();

        return $treatments;
    }

    /**
     * Create a new appointment record.
     *
     * @param array $data The validated data for creating the appointment
     * @return Appointment The newly created appointment model
     */
    public function createAppointment($patient1=null, array $data1 = []): Appointment
    {
        $user = Auth::user();
        return DB::transaction(function() use ($data1, $user) {
            if(!$data1['IsExistingPatient']) {
                $lastRecord = Patient::orderBy('CaseID', 'desc')->first();
                 $data = EntityDataHelper::prepareForCreation($data1);
                 $data['RegistrationDate'] = array_key_exists('RegistrationDate', $data) && $data['RegistrationDate']? Carbon::parse($data['RegistrationDate']) : Carbon::now();
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
                    'LastUpdatedBy' => $user->UserID,
                    'LastUpdatedOn' => now(),
                    'AddedOn' => now(),
                    'AddedBy' => $user->UserID,
                    'rowguid' => EntityDataHelper::generateRowGuid(),
                    'RegistrationDate' => $data['RegistrationDate'],
                ]);
            } else {
                // Respect global scopes (doctor must not access other doctors' patients)
                $patient = Patient::where('PatientID', $data1['PatientID'])->first();
                if (!$patient) {
                    throw new Exception('Patient not found');
                }

                // Initialize $data with all input data first
                $data = $data1;
                // Then override with patient data
                $data['Title'] = $patient->Title;
                $data['FirstName'] = $patient->FirstName;
                $data['LastName'] = $patient->LastName;
                $data['Gender'] = $patient->Gender;
                $data['Nationality'] = $patient->Nationality;
                $data['Mobile'] = $patient->MobileNumber;
                $data['Age'] = $patient->Age;
                $data['RegistrationDate'] = $patient->RegistrationDate;
            }

            $data['PatientID'] = $patient->PatientID;
            if(array_key_exists('AppointmentID',$data) && $data['AppointmentID']) {
                // Mark old appointment with provided status
                Appointment::where('AppointmentID', $data['AppointmentID'])->update(['Status'=>$data['Status']]);
                if($data['Status']=="Rescheduled"){
                    $data['Status']="Scheduled";
                }
                $waitingAreaPatient = WaitingAreaPatient::where('AppointmentID', $data['AppointmentID'])->where('IsDeleted',0)->first();
                if($waitingAreaPatient && $waitingAreaPatient->AppointmentID){
                    $waitingAreaPatient->update(['IsDeleted'=>1]);
                }
                
            }
            if(array_key_exists('PatientTreatmentDoneID',$data) && $data['PatientTreatmentDoneID']) {
                PatientTreatmentsDone::where('PatientTreatmentDoneID', $data['PatientTreatmentDoneID'])->update(['isDeleted'=>1]);
            }
            $appointment = Appointment::create([
                'ClinicID' => $user->ClinicID,
                'PatientID' => $data['PatientID'],
                'ProviderID' => $data['ProviderID'],
                'StartDateTime' => $data['StartDateTime'],
                'EndDateTime' => $data['EndDateTime'],
                'Comments' => $data['Comments'] ?? null,
                'PatientTitle' => $data['Title'],
                'PatientFirstName' => $data['FirstName'],
                'PatientLastName' => $data['LastName'],
                'PatientGender' => $data['Gender'],
                'PatientAge' => $data['Age'],
                'PatientNationality' => $data['Nationality'],
                'PatientPhone' => $data['Mobile'],
                'Status' => $data['Status'],
                'PatientName' => "{$data['Title']} {$data['FirstName']} {$data['LastName']}",
                'CreatedBy' => $user?->UserID ?? null,
                'LastUpdatedBy' => $user?->UserID ?? null,
                'LastUpdatedOn' => now(),
            ]);

            // // Create provider slot for the appointment
            $this->providerSlotService->createProviderSlot([
                'ProviderID' => $data['ProviderID'],
                'StartDatetime' => $data['StartDateTime'],
                'EndDateTime' => $data['EndDateTime'],
                'SlotInterval' => $data['SlotInterval'] ?? 15,
                'CreatedOn' => now(),
                'CreatedBy' => $user?->UserID ?? null,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $user?->UserID ?? null,
                'IsDeleted' => false,
                'rowguid' => Str::uuid(),
            ]);
            // Record reschedule link if an old appointment ID was provided
            if(array_key_exists('AppointmentID',$data) && $data['AppointmentID']) {
                AppointmentRescheduled::create([
                    'OldAppointmentID' => $data['AppointmentID'],
                    'NewAppointmentID' => $appointment->AppointmentID,
                    'Reason' => $data['RescheduleReason'] ?? null,
                    'CreatedBy' => $user?->UserID ?? null,
                ]);
            }

            return $appointment;
        });
    }

    /**
     * Update an existing appointment record.
     *
     * @param Appointment $appointment The appointment model to update
     * @param array $data The validated data for updating the appointment
     * @return Appointment The updated appointment model
     */
    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);
        $appointment->fresh();

        return $appointment;
    }
}
