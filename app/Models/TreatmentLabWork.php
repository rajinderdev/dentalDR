<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class TreatmentLabWork
 *
 * @property string $TreatmentLabWorkID
 * @property string $PatientTreatmentsDoneID
 * @property string $PatientID
 * @property string $ProviderID
 * @property string $PatientLabID
 * @property string|null $CreatedBy
 * @property Carbon|null $CreatedOn
 * @property string|null $LastUpdatedBy
 * @property Carbon|null $LastUpdatedOn
 * @property Carbon|null $LabWorkDate
 */
class TreatmentLabWork extends Model
{
    protected $table = 'TreatmentLabWork';
    protected $primaryKey = 'TreatmentLabWorkID';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
        'LabWorkDate' => 'datetime',
    ];

    protected $fillable = [
        'PatientTreatmentsDoneID',
        'PatientID',
        'ProviderID',
        'PatientLabID',
        'CreatedBy',
        'CreatedOn',
        'LastUpdatedBy',
        'LastUpdatedOn',
        'LabWorkDate',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->TreatmentLabWorkID)) {
                $model->TreatmentLabWorkID = (string) Str::uuid();
            }
            if (empty($model->CreatedOn)) {
                $model->CreatedOn = now();
            }
        });
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['TreatmentLabWorkID'],
        );
    }
}


