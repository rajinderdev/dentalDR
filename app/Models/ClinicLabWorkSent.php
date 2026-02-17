<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicLabWorkSent extends Model
{
    protected $table = 'clinic_lab_work_sent';
    
    protected $primaryKey = 'LabWorkSentID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'LabWorkID',
        'SentID',
        'SentDate',
        'IsDeleted',
        'CreatedBy',
        'CreatedOn',
        'LastUpdatedOn',
        'LastUpdatedOn',
    ];

    protected $dates = [
        'SentDate',
        'CreatedOn',
        'LastUpdatedOn',
    ];

    /**
     * Get the lab work that owns the sent item.
     */
    public function labWork(): BelongsTo
    {
        return $this->belongsTo(ClinicLabWork::class, 'LabWorkID', 'LabWorkID');
    }
}
