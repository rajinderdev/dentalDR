<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonalReminderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ClinicID' => 'required|string|max:255',
            'PatientID' => 'required|string|max:255',
            'ProviderID' => 'required|string|max:255',
            'ReminderDate' => 'required|date',
            'ReminderSubject' => 'required|string|max:255',
            'ReminderDescription' => 'required|string',
            'StatusId' => 'nullable|string|max:255',
        ];
    }
}