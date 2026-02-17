<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class Designation
 * 
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property bool $IsDeleted
 * @property Carbon $CreatedOn
 * @property string|null $CreatedBy
 * @property Carbon $LastUpdatedOn
 * @property string|null $LastUpdatedBy
 * 
 * @property Collection|Patient[] $patients
 *
 * @package App\Models
 */
class Designation extends Model
{
    protected $table = 'designations';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    protected $casts = [
        'is_active' => 'bool',
        'IsDeleted' => 'bool',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'IsDeleted',
        'CreatedOn',
        'CreatedBy',
        'LastUpdatedOn',
        'LastUpdatedBy'
    ];
    
    protected $primaryKey = 'id';
    
    protected $dates = [
        'CreatedOn',
        'LastUpdatedOn'
    ];

  
    /**
     * Boot method to handle model events
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->CreatedBy = Auth::id();
                $model->LastUpdatedBy = Auth::id();
            }
            $model->CreatedOn = now();
            $model->LastUpdatedOn = now();
            $model->IsDeleted = false;
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->LastUpdatedBy = Auth::id();
            }
            $model->LastUpdatedOn = now();
        });
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'Designation', 'name');
    }

    /**
     * Get the formatted created on attribute
     */
    public function getFormattedCreatedOnAttribute()
    {
        return $this->CreatedOn ? $this->CreatedOn->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get the formatted last updated on attribute
     */
    public function getFormattedLastUpdatedOnAttribute()
    {
        return $this->LastUpdatedOn ? $this->LastUpdatedOn->format('Y-m-d H:i:s') : null;
    }
    
    /**
     * Scope a query to only include active records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include non-deleted records.
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('IsDeleted', false);
    }
}
