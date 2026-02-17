<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all authenticated users
    }

    public function rules(): array
    {
        return [
            'Status' => 'required|string|in:Pending,Active,Scheduled,Cancelled,Completed',
            'Comments' => 'nullable|string',
        ];
    }
}
