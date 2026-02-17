<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRelationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $patientRelationId = $this->route('patient_relation')?->PatientRelationID;
        $patientId = $this->input('PatientID');
        $relatedPatientId = $this->input('RelatedPatientID');
        
        $validRelationTypes = [
            'Parent', 'Child', 'Spouse', 'Sibling', 'Guardian', 'Other',
            'Mother', 'Father', 'Son', 'Daughter', 'Brother', 'Sister',
            'Grandparent', 'Grandchild', 'Aunt', 'Uncle', 'Niece', 'Nephew',
            'Cousin', 'Step-Parent', 'Step-Child', 'Step-Sibling', 'Other Relative'
        ];

        // Base rules that don't depend on the relation type
        $rules = [
            'PatientID' => [
                'required',
                'string',
                'exists:Patient,PatientID',
                'different:RelatedPatientID', // Prevent self-relation
            ],
            'RelatedPatientID' => [
                'required',
                'string',
                'exists:Patient,PatientID',
                'different:PatientID', // Prevent self-relation
            ],
            'Relation' => [
                'required',
                'string',
                'max:100',
                Rule::in($validRelationTypes),
            ],
            'Notes' => 'nullable|string|max:1000',
            'IsActive' => 'sometimes|boolean',
        ];

        // Only add the relation-specific validations if we have a valid relation type
        if ($this->has('Relation') && in_array($this->input('Relation'), $validRelationTypes)) {
            $relationType = $this->input('Relation');
            
            // Add validation for checking if the reverse relation already exists
            $rules['PatientID'][] = function ($attribute, $value, $fail) use ($relatedPatientId, $relationType, $patientRelationId) {
                // Check if the reverse relation already exists
                $exists = \App\Models\PatientRelation::where('PatientID', $relatedPatientId)
                    ->where('RelatedPatientID', $value)
                    ->where('Relation', $this->getReverseRelationType($relationType))
                    ->when($patientRelationId, function ($query) use ($patientRelationId) {
                        return $query->where('PatientRelationID', '!=', $patientRelationId);
                    })
                    ->exists();

                if ($exists) {
                    $fail('A reverse relation already exists between these patients.');
                }
            };

            // Add validation for checking if the relation already exists
            $rules['RelatedPatientID'][] = function ($attribute, $value, $fail) use ($patientId, $relationType, $patientRelationId) {
                // Check if relation already exists
                $exists = \App\Models\PatientRelation::where('PatientID', $patientId)
                    ->where('RelatedPatientID', $value)
                    ->where('Relation', $relationType)
                    ->when($patientRelationId, function ($query) use ($patientRelationId) {
                        return $query->where('PatientRelationID', '!=', $patientRelationId);
                    })
                    ->exists();

                if ($exists) {
                    $fail('This relation already exists between these patients.');
                }
            };
        }

        return $rules;
    }

    /**
     * Get the reverse relation type for validation
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

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'PatientID.different' => 'A patient cannot be related to themselves.',
            'RelatedPatientID.different' => 'A patient cannot be related to themselves.',
            'Relation.in' => 'The selected relation is invalid.',
        ];
    }
}
