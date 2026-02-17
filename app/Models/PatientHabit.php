<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientHabit extends Model
{
    protected $primaryKey = 'PatientHabitID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'PatientHabitID',
        'PatientID',
        'HabitID',
        'Notes',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy',
        'CreatedOn',
        'LastUpdatedOn',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class, 'HabitID', 'HabitID');
    }
}
