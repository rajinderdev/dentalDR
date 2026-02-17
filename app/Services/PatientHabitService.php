<?php

namespace App\Services;

use App\Models\PatientHabit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;

class PatientHabitService
{
    /**
     * Get all patient habits with pagination
     */
    public function getAllPatientHabits(int $perPage = 15): LengthAwarePaginator
    {
        return PatientHabit::with(['patient', 'habit'])->where('IsDeleted', false)
            ->latest('CreatedOn')
            ->paginate($perPage);
    }

    /**
     * Get patient habits by patient ID
     */
    public function getPatientHabitsByPatient(string $patientId): Collection
    {
        return PatientHabit::with('habit')
            ->where('PatientID', $patientId)
            ->where('IsDeleted', false)
            ->get();
    }

    /**
     * Get a specific patient habit by ID
     */
    public function getPatientHabitById(string $patientId, string $patientHabitId): ?PatientHabit
    {
        return PatientHabit::with(['patient', 'habit'])->where('IsDeleted', false)->findOrFail($patientHabitId);
    }

    /**
     * Create a new patient habit
     */
    public function createPatientHabit(array $data): PatientHabit
    {
        $dataToPersist = EntityDataHelper::prepareForCreation($data);
        $dataToPersist['PatientHabitID'] = (string) Str::uuid();
        
        return PatientHabit::create($dataToPersist);
    }

    /**
     * Update an existing patient habit
     */
    /**
     * Update an existing patient habit
     *
     * @param string $patientId
     * @param string $patientHabitId
     * @param array $data
     * @return PatientHabit
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updatePatientHabit(string $patientId, string $patientHabitId, array $data): PatientHabit
    {
        $dataToUpdate = EntityDataHelper::prepareForUpdate($data);
        $patientHabit =  PatientHabit::findOrFail($patientHabitId);
        
        // Verify the habit belongs to the specified patient
        if ($patientHabit->PatientID !== $patientId) {
            throw new \InvalidArgumentException('Patient habit does not belong to the specified patient');
        }
        
        $patientHabit->update($dataToUpdate);
        
        return $patientHabit->fresh();
    }

    /**
     * Delete a patient habit (soft delete)
     */
     public function deletePatientHabit(string $patientId, array $data): ?bool
    {
        return PatientHabit::whereIn('PatientHabitID', $data)->update(['IsDeleted' => true]);
    }

    /**
     * Delete all habits for a specific patient
     *
     * @param string $patientId
     * @return int Number of deleted records
     */
    public function deleteAllPatientHabits(string $patientId): int
    {
        return PatientHabit::where('PatientID', $patientId)
            ->update(['IsDeleted' => true]);
    }
}
