<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\Clinic;
use App\Traits\ApiResponse;
use Exception;
class TermsAndCondtionsController extends Controller
{
   use ApiResponse;
  public function index(Request $request)
  {
    $patient = null;
    $doctor = null;
    $clinic = null;
    if ($request->has('PatientID')) {
      $patient = Patient::find($request->PatientID);
       Session::put('PatientID', $request->PatientID);
    }
    else{
       $patientId = Session::get('PatientID');
       $patient = Patient::find($patientId);
    }
    if ($patient) {
      $doctor = Provider::find($patient->ProviderID);
    }
    if($doctor){
      $clinic = Clinic::find($doctor->ClinicID);
    }

    return view('termsandconditions.terms-and-conditions', compact('patient', 'doctor', 'clinic'));
  }
  public function getData($patientId)
  {
    $patient = null;
    $doctor = null;
    $clinic = null;
    if ($patientId) {
      $patient = Patient::find($patientId);
    }
    if ($patient) {
      $doctor = Provider::find($patient->ProviderID);
    }
    if($doctor){
      $clinic = Clinic::find($doctor->ClinicID);
    }

    return  $this->successResponse([
                'patient' => $patient,
                'doctor' => $doctor,
                'clinic' => $clinic,
            ]);
  }

}
