<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalletRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $walletId = $this->route('wallet')?->WalletID;
        
        $rules = [
            'PatientID' => [
                'required',
                'string',
                'exists:Patient,PatientID',
                $this->isMethod('POST') ? Rule::unique('patient_wallets', 'PatientID') : null,
            ],
            'Currency' => [
                'required',
                'string',
                'size:3',
                'uppercase',
            ],
            'IsActive' => [
                'sometimes',
                'boolean',
            ],
        ];

        // Only allow InitialBalance on create
        if ($this->isMethod('POST')) {
            $rules['InitialBalance'] = [
                'nullable',
                'numeric',
                'min:0',
                'max:1000000', // Adjust max as needed
            ];
        }

        // Remove null values from rules
        return array_filter($rules);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'PatientID.unique' => 'A wallet already exists for this patient.',
            'InitialBalance.min' => 'Initial balance cannot be negative.',
            'InitialBalance.max' => 'Initial balance is too large.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('currency')) {
            $this->merge([
                'Currency' => strtoupper($this->currency),
            ]);
        }
    }
}
