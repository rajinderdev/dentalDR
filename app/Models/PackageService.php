<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageService extends Model
{
    public $timestamps = false;
    
    protected $primaryKey = 'PackageServiceID';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'CreatedOn';
    const UPDATED_AT = 'LastUpdatedOn';

    protected $fillable = [
        'PackageServiceID',
        'PackageID',
        'ClinicID',
        'TreatmentTypeID',
        'TreatmentName',
        'QuantityLimit',
        'IsDeleted',
        'CreatedBy',
        'CreatedOn',
        'LastUpdatedBy',
        'LastUpdatedOn'
    ];

    protected $casts = [
        'QuantityLimit' => 'integer',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'PackageID', 'PackageID');
    }

    public function treatmentTypeHierarchy()
    {
        return $this->belongsTo(TreatmentTypeHierarchy::class, 'TreatmentTypeID', 'TreatmentTypeID');
    }
}
