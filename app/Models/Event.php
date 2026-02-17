<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Event
 * 
 * @property string $EventID
 * @property string|null $Description
 * @property Carbon|null $StartDateTime
 * @property Carbon|null $EndDateTime
 * @property string|null $CreatedBy
 * @property Carbon|null $CreatedOn
 * @property string|null $LastUpdatedBy
 * @property Carbon|null $LastUpdatedOn
 * @property string|null $Status
 *
 * @package App\Models
 */
class Event extends Model
{
    protected $table = 'Events';
    protected $primaryKey = 'EventID';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'StartDateTime' => 'datetime',
        'EndDateTime' => 'datetime',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
    ];

    protected $fillable = [
        'Description',
        'StartDateTime',
        'EndDateTime',
        'CreatedBy',
        'CreatedOn',
        'LastUpdatedBy',
        'LastUpdatedOn',
        'Status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->EventID)) {
                $model->EventID = (string) Str::uuid();
            }
            if (empty($model->CreatedOn)) {
                $model->CreatedOn = now();
            }
        });
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['EventID'],
        );
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'CreatedBy', 'UserID');
    }
}
