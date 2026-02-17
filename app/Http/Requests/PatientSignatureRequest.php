<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientSignatureRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Signatures' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
