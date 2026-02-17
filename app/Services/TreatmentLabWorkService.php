<?php

namespace App\Services;

use App\Models\TreatmentLabWork;
use App\Models\Patient;
use Illuminate\Support\Carbon;
use App\Helpers\EntityDataHelper;
class TreatmentLabWorkService
{
    public function getByTreatmentDoneId(Patient $patient,string $patientTreatmentsDoneId, int $perPage = 50)
    {
        return TreatmentLabWork::where('PatientTreatmentsDoneID', $patientTreatmentsDoneId)
            ->orderBy('CreatedOn', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): TreatmentLabWork
    {
        // Normalize dates
        if (!empty($data['LabWorkDate'])) {
            try { $data['LabWorkDate'] = Carbon::parse($data['LabWorkDate']); } catch (\Throwable $e) { $data['LabWorkDate'] = null; }
        }
      
        $data = EntityDataHelper::prepareForCreation($data);
        return TreatmentLabWork::create($data);
    }
}


