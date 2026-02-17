<?php

namespace App\Services;

use App\Models\PatientRelation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;

class PatientRelationService
{
    /**
     * Get all patient relations with pagination
     */
    public function getAllRelations(int $perPage = 15): LengthAwarePaginator
    {
        return PatientRelation::with(['patient', 'relatedPatient'])
            ->where('IsDeleted', false)
            ->latest('CreatedOn')
            ->paginate($perPage);
    }

    /**
     * Get relations by patient ID
     */
    public function getRelationsByPatient(string $patientId): Collection
    {
        return PatientRelation::with('relatedPatient')
            ->where('PatientID', $patientId)
            ->where('IsDeleted', false)
            ->orderBy('CreatedOn', 'desc')
            ->get();
    }

    /**
     * Get a specific relation by ID
     */
    public function getRelationById(string $relationId): ?PatientRelation
    {
        return PatientRelation::with(['patient', 'relatedPatient'])
            ->where('PatientRelationID', $relationId)
            ->where('IsDeleted', false)
            ->firstOrFail();
    }

    /**
     * Create a new patient relation
     */
    public function createRelation(array $data): PatientRelation
    {
        return DB::transaction(function () use ($data) {
            $dataToPersist = EntityDataHelper::prepareForCreation($data);
            $dataToPersist['PatientRelationID'] = (string) Str::uuid();
            
            $relation = PatientRelation::create($dataToPersist);
            
            // Create reverse relation if it's a bidirectional relationship
            $this->createReverseRelation($relation);
            
            return $relation->load(['patient', 'relatedPatient']);
        });
    }

    /**
     * Update an existing patient relation
     */
    public function updateRelation(PatientRelation $relation, array $data): PatientRelation
    {
        return DB::transaction(function () use ($relation, $data) {
            $dataToUpdate = EntityDataHelper::prepareForUpdate($data);
            $relation->update($dataToUpdate);
            
            // Update reverse relation if it exists
            $this->updateReverseRelation($relation);
            
            return $relation->fresh(['patient', 'relatedPatient']);
        });
    }

    /**
     * Delete a patient relation (soft delete)
     */
    public function deleteRelation(PatientRelation $relation): bool
    {
        return DB::transaction(function () use ($relation) {
            // Soft delete the relation
            $result = $relation->update(['IsDeleted' => true]);
            
            // Also soft delete the reverse relation if it exists
            $this->deleteReverseRelation($relation);
            
            return $result;
        });
    }

    /**
     * Create a reverse relation for bidirectional relationships
     */
    private function createReverseRelation(PatientRelation $relation): void
    {
        $reverseRelationType = $this->getReverseRelationType($relation->Relation);
        
        // Skip if the relation type doesn't have a reverse or is the same (like Spouse)
        if ($reverseRelationType === $relation->Relation && $relation->Relation !== 'Spouse') {
            return;
        }
        
        // Check if reverse relation already exists
        $exists = PatientRelation::where('PatientID', $relation->RelatedPatientID)
            ->where('RelatedPatientID', $relation->PatientID)
            ->where('Relation', $reverseRelationType)
            ->exists();
            
        if (!$exists) {
            PatientRelation::create([
                'PatientRelationID' => (string) Str::uuid(),
                'PatientID' => $relation->RelatedPatientID,
                'RelatedPatientID' => $relation->PatientID,
                'Relation' => $reverseRelationType,
                'Notes' => $relation->Notes,
                'IsActive' => $relation->IsActive,
                'CreatedBy' => $relation->CreatedBy,
                'LastUpdatedBy' => $relation->LastUpdatedBy,
                'CreatedOn' => now(),
                'LastUpdatedOn' => now(),
            ]);
        }
    }

    /**
     * Update reverse relation when the main relation is updated
     */
    private function updateReverseRelation(PatientRelation $relation): void
    {
        $reverseRelationType = $this->getReverseRelationType($relation->Relation);
        
        // Find and update the reverse relation
        $reverseRelation = PatientRelation::where('PatientID', $relation->RelatedPatientID)
            ->where('RelatedPatientID', $relation->PatientID)
            ->where('Relation', $reverseRelationType)
            ->first();
            
        if ($reverseRelation) {
            $reverseRelation->update([
                'Notes' => $relation->Notes,
                'IsActive' => $relation->IsActive,
                'LastUpdatedBy' => $relation->LastUpdatedBy,
                'LastUpdatedOn' => now(),
            ]);
        }
    }

    /**
     * Delete reverse relation when the main relation is deleted
     */
    private function deleteReverseRelation(PatientRelation $relation): void
    {
        $reverseRelationType = $this->getReverseRelationType($relation->Relation);
        
        // Find and soft delete the reverse relation
        $reverseRelation = PatientRelation::where('PatientID', $relation->RelatedPatientID)
            ->where('RelatedPatientID', $relation->PatientID)
            ->where('Relation', $reverseRelationType)
            ->first();
            
        if ($reverseRelation) {
            $reverseRelation->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => $relation->LastUpdatedBy,
                'LastUpdatedOn' => now(),
            ]);
        }
    }

    /**
     * Get the reverse relation type
     */
    private function getReverseRelationType(string $relationType): string
    {
        $reverseRelations = [
            'Parent' => 'Child',
            'Child' => 'Parent',
            'Spouse' => 'Spouse',
            'Sibling' => 'Sibling',
            'Guardian' => 'Dependent',
            'Dependent' => 'Guardian',
            'Mother' => 'Child',
            'Father' => 'Child',
            'Son' => 'Parent',
            'Daughter' => 'Parent',
            'Brother' => 'Sibling',
            'Sister' => 'Sibling',
            'Grandparent' => 'Grandchild',
            'Grandchild' => 'Grandparent',
            'Aunt' => 'Niece/Nephew',
            'Uncle' => 'Niece/Nephew',
            'Niece' => 'Aunt/Uncle',
            'Nephew' => 'Aunt/Uncle',
            'Cousin' => 'Cousin',
            'Step-Parent' => 'Step-Child',
            'Step-Child' => 'Step-Parent',
            'Step-Sibling' => 'Step-Sibling',
            'Other Relative' => 'Other Relative',
            'Other' => 'Other'
        ];

        return $reverseRelations[$relationType] ?? 'Other';
    }
}
