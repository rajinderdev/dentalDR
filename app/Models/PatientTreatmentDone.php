<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class PatientTreatmentDone extends Model
{
    use HasUuid;
    
    protected $primaryKey = 'PatientTreatmentDoneID';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PatientTreatmentDoneID',
        'PatientID',
        'TreatmentID',
        'ToothNumber',
        'Surface',
        'Status',
        'Notes',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy',
        'TreatmentDate',
        'ProviderID'
    ];

    protected $casts = [
        'TreatmentDate' => 'date',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'TreatmentID', 'TreatmentID');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'ProviderID', 'UserID');
    }
}
