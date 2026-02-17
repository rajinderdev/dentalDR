<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTransactionRequest extends FormRequest
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
			'EmailTypeID' => 'required|max:255',
			'EmailTo' => 'required|email|max:255',
			'EmailFrom' => 'required|email|max:255',
			'EmailCC' => 'required|email|max:255',
			'EmailBcc' => 'required|email|max:255',
			'Subject' => 'required|string',
			'MessageText' => 'required|string',
			'EmailAttachmentsID' => 'required|max:255',
			'Status' => 'required|string',
			'SentOn' => 'required|string',
			'IsDeleted' => 'required',
			'EmailFromName' => 'required|max:255',
			'EmailToName' => 'required|max:255',
			'ScheduledOn' => 'required',
        ];
    }
}