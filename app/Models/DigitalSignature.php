<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class DigitalSignature
 * 
 * @property string $id
 * @property string $patient_id
 * @property string|null $provider_id
 * @property string|null $clinic_id
 * @property string $patient_name
 * @property string|null $patient_email
 * @property string|null $patient_phone
 * @property string $signature_data
 * @property \Carbon\Carbon $signature_datetime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class DigitalSignature extends Model
{
    use HasUuids;

    protected $table = 'digital_signatures';
    protected $primaryKey = 'DigitalSignaturesID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'PatientID',
        'ProviderID',
        'patient_name',
        'signature_data',
        'signature_datetime',
        'CreatedOn',
        'CreatedBy',
        'LastUpdatedOn',
        'LastUpdatedBy',
        'rowguid'
    ];

    protected $casts = [
        'DigitalSignaturesID' => 'string',
        'PatientID' => 'string',
        'ProviderID' => 'string',
        'patient_name' => 'string',
        'signature_data' => 'string',
        'signature_datetime' => 'datetime',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
        'LastUpdatedBy' => 'string',
        'rowguid' => 'string'
    ];
}
