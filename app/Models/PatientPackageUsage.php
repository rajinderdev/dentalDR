<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class PatientPackageUsage extends Model
{
    use HasUuid;
    
    protected $primaryKey = 'PatientPackageUsageID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'patient_package_usage';

    protected $fillable = [
        'PatientPackageUsageID',
        'ClinicID',
        'PatientID',
        'PatientPackageID',
        'PatientTreatmentDoneID',
        'ProviderID',
        'TreatmentDate',
        'Notes',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy'
    ];

    protected $casts = [
        'TreatmentDate' => 'date',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    public function patientPackage()
    {
        return $this->belongsTo(PatientPackage::class, 'PatientPackageID', 'PatientPackageID');
    }

    public function treatment()
    {
        return $this->belongsTo(PatientTreatmentDone::class, 'PatientTreatmentDoneID', 'PatientTreatmentDoneID');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'ProviderID', 'UserID');
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }
}
