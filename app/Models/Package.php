<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    public $timestamps = false;
    
    protected $primaryKey = 'PackageID';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'CreatedOn';
    const UPDATED_AT = 'LastUpdatedOn';

    protected $fillable = [
        'PackageID',
        'ClinicID',
        'PackageName',
        'PackageCode',
        'Description',
        'Price',
        'Interval',
        'DiscountAmount',
        'AdditionAmount',
        'Status',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy',
        'CreatedOn',
        'LastUpdatedOn'
    ];

    protected $casts = [
        'Price' => 'decimal:2',
        'DiscountAmount' => 'decimal:2',
        'AdditionAmount' => 'decimal:2',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
    ];

    public function services()
    {
        return $this->hasMany(PackageService::class, 'PackageID', 'PackageID');
    }
}
