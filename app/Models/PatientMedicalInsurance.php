<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientMedicalInsurance extends Model
{
    protected $primaryKey = 'PatientMedicalInsuranceID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'PatientMedicalInsuranceID',
        'PatientID',
        'InsuranceProvider',
        'PolicyNumber',
        'ExpirationDate',
        'Notes',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy',
        'CreatedOn',
        'LastUpdatedOn'
    ];

    protected $casts = [
        'ExpirationDate' => 'date',
        'IsActive' => 'boolean',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    /**
     * Get the patient that owns the insurance.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }

    /**
     * Scope a query to only include active insurances.
     */
    public function scopeActive($query)
    {
        return $query->where('IsActive', true)
                    ->where('IsDeleted', false)
                    ->where(function($q) {
                        $q->whereNull('ExpirationDate')
                          ->orWhere('ExpirationDate', '>=', now()->toDateString());
                    });
    }
    public function getRouteKeyName()
    {
        return 'PatientMedicalInsuranceID';
    }
}
