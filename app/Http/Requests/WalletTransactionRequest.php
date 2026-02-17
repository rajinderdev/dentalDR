<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalletTransactionRequest extends FormRequest
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
        return [
            'Amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:1000000', // Adjust max as needed
            ],
            'TransactionType' => [
                'required',
                'string',
                Rule::in(['CREDIT', 'DEBIT']),
            ],
            'ReferenceType' => [
                'nullable',
                'string',
                'max:50',
            ],
            'ReferenceID' => [
                'nullable',
                'string',
                'max:36',
            ],
            'Description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'Amount.min' => 'The amount must be at least :min.',
            'Amount.max' => 'The amount may not be greater than :max.',
            'TransactionType.in' => 'Transaction type must be either CREDIT or DEBIT.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('transaction_type')) {
            $this->merge([
                'TransactionType' => strtoupper($this->transaction_type),
            ]);
        }
        
        if ($this->has('reference_type')) {
            $this->merge([
                'ReferenceType' => $this->reference_type,
            ]);
        }
        
        if ($this->has('reference_id')) {
            $this->merge([
                'ReferenceID' => $this->reference_id,
            ]);
        }
        
        if ($this->has('description')) {
            $this->merge([
                'Description' => $this->description,
            ]);
        }
    }
}
