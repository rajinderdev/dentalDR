<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    public $timestamps = false;
    
    protected $primaryKey = 'HabitID';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'CreatedOn';
    const UPDATED_AT = 'LastUpdatedOn';

    protected $fillable = [
        'HabitID',
        'Name',
        'Description',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'LastUpdatedBy',
        'CreatedOn',
        'LastUpdatedOn'
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'IsDeleted' => 'boolean',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
    ];
}
