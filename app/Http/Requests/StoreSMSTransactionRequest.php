<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSMSTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'ClinicID' => 'required|string|max:255',
			'ReferenceCode' => 'required|string',
			'PatientID' => 'required|string|max:255',
			'SMSTypeID' => 'required|string|max:255',
			'MobileNumber' => 'required|string',
			'MessageText' => 'required|string',
			'ScheduledOn' => 'required|string',
			'SentStatus' => 'required|string',
			'SentOn' => 'required',
			'SentStatusMessage' => 'required|string',
			'IsPromotional' => 'nullable',
        ];
    }
}