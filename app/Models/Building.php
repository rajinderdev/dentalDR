<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Class Building
 * 
 * @property int $id
 * @property string $building_name
 * @property string $building_code
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $area
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $pincode
 * @property bool $status
 * @property bool $IsDeleted
 * @property Carbon $CreatedOn
 * @property string|null $CreatedBy
 * @property Carbon $LastUpdatedOn
 * @property string|null $LastUpdatedBy
 *
 * @package App\Models
 */
class Building extends Model
{
    protected $table = 'buildings';
    public $timestamps = false;
    
    // Specify the name of the "created at" column.
    const CREATED_AT = 'CreatedOn';
    
    // Specify the name of the "updated at" column.
    const UPDATED_AT = 'LastUpdatedOn';

    protected $casts = [
        'status' => 'bool',
        'IsDeleted' => 'bool',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime'
    ];

    protected $fillable = [
        'building_name',
        'building_code',
        'address1',
        'address2',
        'area',
        'city',
        'state',
        'country',
        'pincode',
        'status',
        'IsDeleted',
        'CreatedOn',
        'CreatedBy',
        'LastUpdatedOn',
        'LastUpdatedBy'
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
            $model->status = true;
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->LastUpdatedBy = Auth::id();
            }
            $model->LastUpdatedOn = now();
        });
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
        return $query->where('status', true);
    }
    
    /**
     * Scope a query to only include non-deleted records.
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('IsDeleted', false);
    }
}
