<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LicenseModules extends Model
{
    protected $table = 'LicenseModules';
    protected $primaryKey = 'LicenseModuleID';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'LicenseModuleID' => 'string',
        'OrderNumber' => 'integer',
        'CreatedOn' => 'datetime'
    ];

    protected $fillable = [
        'ModuleCode',
        'ModuleName',
        'ModuleDescription',
        'OrderNumber',
        'PreRequisitesCSV',
        'CreatedOn',
        'CreatedBy'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->LicenseModuleID)) {
                $model->LicenseModuleID = (string) Str::uuid();
            }
        });
    }
}
