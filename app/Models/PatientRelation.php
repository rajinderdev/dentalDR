<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientRelation extends Model
{
    protected $primaryKey = 'PatientRelationID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'PatientRelationID',
        'PatientID',
        'RelatedPatientID',
        'Relation',
        'Notes',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy',
        'CreatedOn',
        'LastUpdatedOn'
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    /**
     * Get the patient who owns this relation
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }

    /**
     * Get the related patient
     */
    public function relatedPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'RelatedPatientID', 'PatientID');
    }
}
