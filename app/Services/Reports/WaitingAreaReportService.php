<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

class WaitingAreaReportService
{
    public function getWaitingAreaReport(array $filters, int $perPage = 50): array
    {
        $firstAddedSub = DB::table('PatientTreatmentsDone as ptd')
            ->select([
                'ptd.WaitingAreaID',
                DB::raw('MIN(ptd.AddedOn) as first_added_on'),
            ])
            ->where(function ($q) {
                $q->whereNull('ptd.IsDeleted')->orWhere('ptd.IsDeleted', 0);
            })
            ->whereNotNull('ptd.WaitingAreaID')
            ->whereNotNull('ptd.AddedOn')
            ->groupBy('ptd.WaitingAreaID');

        $lastChildAddedSub = DB::table('PatientTreatmentsDone as ptd')
            ->select([
                'ptd.ParentPatientTreatmentDoneID',
                DB::raw('MAX(ptd.AddedOn) as last_child_added_on'),
            ])
            ->where(function ($q) {
                $q->whereNull('ptd.IsDeleted')->orWhere('ptd.IsDeleted', 0);
            })
            ->whereNotNull('ptd.ParentPatientTreatmentDoneID')
            ->whereNotNull('ptd.AddedOn')
            ->groupBy('ptd.ParentPatientTreatmentDoneID');

        $query = DB::table('WaitingAreaPatients')
            ->leftJoin('Patient', 'WaitingAreaPatients.PatientID', '=', 'Patient.PatientID')
            ->leftJoin('Appointments', 'WaitingAreaPatients.AppointmentID', '=', 'Appointments.AppointmentID')
            ->leftJoin('Provider', 'WaitingAreaPatients.ProviderID', '=', 'Provider.ProviderID')
            ->leftJoinSub($firstAddedSub, 'ptd_first_added', function ($join) {
                $join->on('WaitingAreaPatients.WaitingAreaID', '=', 'ptd_first_added.WaitingAreaID');
            })
            ->leftJoin('PatientTreatmentsDone as ptd_first', function ($join) {
                $join->on('ptd_first.WaitingAreaID', '=', 'WaitingAreaPatients.WaitingAreaID')
                    ->on('ptd_first.AddedOn', '=', 'ptd_first_added.first_added_on');
            })
            ->leftJoinSub($lastChildAddedSub, 'ptd_last_child_added', function ($join) {
                $join->on('ptd_last_child_added.ParentPatientTreatmentDoneID', '=', 'ptd_first.PatientTreatmentDoneID');
            })
            ->leftJoin('PatientTreatmentsDone as ptd_last', function ($join) {
                $join->on('ptd_last.ParentPatientTreatmentDoneID', '=', 'ptd_first.PatientTreatmentDoneID')
                    ->on('ptd_last.AddedOn', '=', 'ptd_last_child_added.last_child_added_on');
            })
            ->select([
                DB::raw("TRIM(CONCAT(COALESCE(Patient.Title, ''), ' ', COALESCE(Patient.FirstName, ''), ' ', COALESCE(Patient.LastName, ''))) as patient_name"),
                DB::raw("IF(WaitingAreaPatients.AppointmentID IS NULL, 'Direct Checkin', Appointments.StartDateTime) as appointment_time"),
                DB::raw("TIME_FORMAT(WaitingAreaPatients.ArrivalTime, '%h:%i %p') as check_in"),
                DB::raw('COALESCE(WaitingAreaPatients.WaitTime, TIMEDIFF(WaitingAreaPatients.OperationTime, WaitingAreaPatients.ArrivalTime)) as waiting_time'),
                DB::raw("TIME_FORMAT(COALESCE(ptd_first.AddedOn, ptd_first.TreatmentDate, WaitingAreaPatients.OperationTime, WaitingAreaPatients.ArrivalTime), '%h:%i %p') as treatment_start_time"),
                DB::raw("TIME_FORMAT(COALESCE(ptd_last.AddedOn, ptd_first.AddedOn, ptd_last.CompletionTime, ptd_first.CompletionTime, WaitingAreaPatients.CompleteTime), '%h:%i %p') as treatment_completed_time"),
                DB::raw('TIMEDIFF(COALESCE(ptd_last.AddedOn, ptd_first.AddedOn, ptd_last.CompletionTime, ptd_first.CompletionTime, WaitingAreaPatients.CompleteTime), COALESCE(ptd_first.AddedOn, ptd_first.TreatmentDate, WaitingAreaPatients.OperationTime, WaitingAreaPatients.ArrivalTime)) as treatment_time'),
                DB::raw("CASE WHEN COALESCE(ptd_last.IsCompleted, ptd_first.IsCompleted, 0) = 1 OR WaitingAreaPatients.CompleteTime IS NOT NULL THEN 'Treatment Completed' ELSE 'Waiting' END as status"),
                'Provider.ProviderName as doctor_name',
            ])
            ->where('WaitingAreaPatients.MovedToTreatmentArea', 1)
            ->where(function ($q) {
                $q->whereNull('WaitingAreaPatients.IsDeleted')->orWhere('WaitingAreaPatients.IsDeleted', 0);
            });

        if (!empty($filters['start_date'])) {
            $query->whereDate('WaitingAreaPatients.ArrivalTime', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('WaitingAreaPatients.ArrivalTime', '<=', $filters['end_date']);
        }
        if (!empty($filters['doctor_id']) && $filters['doctor_id'] !== 'all') {
            $query->where('WaitingAreaPatients.ProviderID', '=', $filters['doctor_id']);
        }

        $query->orderBy('WaitingAreaPatients.ArrivalTime', 'desc');

        $perPage = $perPage > 0 ? $perPage : 50;
        $results = $query->paginate($perPage);

        return [
            'data' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
            ],
        ];
    }
}
