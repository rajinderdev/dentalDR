<?php

namespace App\Services\Reports;

use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorIncentiveReportService
{
    public function getDoctorIncentiveReport(array $filters, int $perPage = 50): array
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $providerIds = $filters['provider_ids'] ?? [];
        $doctor_id = $filters['doctor_id'] ?? null;
        $treatmentTypeIds = $filters['treatment_type_ids'] ?? [];

        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                return [
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => $perPage,
                        'total' => 0,
                        'last_page' => 1,
                    ]
                ];
            }

            // Doctors may only see their own data. If caller provided provider_ids, intersect.
            if (!empty($providerIds) && !in_array($doctorProviderId, $providerIds, true)) {
                return [
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => $perPage,
                        'total' => 0,
                        'last_page' => 1,
                    ]
                ];
            }
            $providerIds = [$doctorProviderId];
        }

        $query = \App\Models\PatientTreatmentsDone::withoutGlobalScopes()
            ->join('PatientTreatmentTypeDone as pttd', 'PatientTreatmentsDone.PatientTreatmentDoneId', '=', 'pttd.PatientTreatmentDoneId')
            ->join('TreatmentTypeHierarchy as tth', 'pttd.TreatmentTypeID', '=', 'tth.TreatmentTypeID')
            ->leftJoin('Patient as p', 'PatientTreatmentsDone.PatientID', '=', 'p.PatientID')
            ->select(
                'tth.Title as TreatmentType',
                'tth.doctorincentive_percentage',
                'tth.TreatmentTypeID',
                'PatientTreatmentsDone.ProviderID',
                'PatientTreatmentsDone.PatientID',
                DB::raw('COUNT(DISTINCT PatientTreatmentsDone.PatientTreatmentDoneId) as NumberOfTreatments'),
                DB::raw('SUM(PatientTreatmentsDone.TreatmentTotalCost) as TotalCost'),
                DB::raw('SUM(PatientTreatmentsDone.TreatmentDiscount) as Discount'),
                DB::raw('SUM(PatientTreatmentsDone.TreatmentTotalCost * tth.doctorincentive_percentage / 100) as Incentive'),
                DB::raw('CONCAT(COALESCE(p.Title, ""), " ", COALESCE(p.FirstName, ""), " ", COALESCE(p.LastName, "")) as patient_name'),
                'p.Gender as patient_gender',
                'p.MobileNumber as patient_mobile',
                'p.EmailAddress1 as patient_email',
                'p.Age as patient_age',
                'p.AddressLine1 as patient_address',
                'p.CaseID as patient_case_id',
                DB::raw('CASE WHEN p.ImagePath IS NOT NULL AND p.ImagePath != "" THEN CONCAT("' . asset('') . '", p.ImagePath) ELSE NULL END as patient_image')
            )
            ->where('PatientTreatmentsDone.isDeleted', 0)
            ->where('pttd.isDeleted', 0)
            ->where('tth.isDeleted', 0)
            ->whereBetween('PatientTreatmentsDone.TreatmentDate', [$startDate, $endDate]);

        if (!empty($providerIds)) {
            $query->whereIn('PatientTreatmentsDone.ProviderID', $providerIds);
        }
        if (!empty($treatmentTypeIds)) {
            $query->whereIn('pttd.TreatmentTypeID', $treatmentTypeIds);
        }
        if (!empty($doctor_id)) {
            $query->where('PatientTreatmentsDone.ProviderID', $doctor_id);
        }
        $query->groupBy('tth.Title', 'PatientTreatmentsDone.ProviderID', 'PatientTreatmentsDone.PatientID', 
            'p.Title', 'p.FirstName', 'p.LastName', 'p.Gender', 'p.MobileNumber', 'p.EmailAddress1', 'p.ImagePath', 'p.Age', 'p.AddressLine1', 'p.CaseID');

        $data = $query->paginate($perPage);

        return [
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
            ]
        ];
    }
 
}
