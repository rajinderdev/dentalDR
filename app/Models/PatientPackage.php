<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
class PatientPackage extends Model
{
    use HasUuid;
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'PatientPackageID';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'PatientPackageID',
        'PatientID',
        'ClinicID',
        'PackageID',
        'StartDate',
        'EndDate',
        'TotalCost',
        'PaymentDate',
        'AmountPaid',
        'PaymentMode',
        'TransactionReference',
        'PaymentStatus',
        'Status',
        'CreatedBy',
        'LastUpdatedBy',
        'IsDeleted'
    ];

    protected $casts = [
        'StartDate' => 'date',
        'EndDate' => 'date',
        'PaymentDate' => 'datetime',
        'TotalCost' => 'decimal:2',
        'AmountPaid' => 'decimal:2',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'PackageID', 'PackageID');
    }
}
