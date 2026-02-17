<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HabitRequest extends FormRequest
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
        $habitId = $this->route('habit') ? $this->route('habit')->id : null;

        return [
            'Name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('habits', 'Name')->ignore($habitId)
            ],
            'Description' => 'nullable|string',
            'IsActive' => 'boolean'
        ];
    }
}
