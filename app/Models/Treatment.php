<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'TreatmentID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'TreatmentID',
        'TreatmentName',
        'Description',
        'Price',
        'Duration',
        'IsActive',
        'ClinicID',
        'CreatedBy',
        'LastUpdatedBy'
    ];

    protected $casts = [
        'Price' => 'decimal:2',
        'Duration' => 'integer',
        'IsActive' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function packageServices()
    {
        return $this->hasMany(PackageService::class, 'TreatmentID', 'TreatmentID');
    }
}
