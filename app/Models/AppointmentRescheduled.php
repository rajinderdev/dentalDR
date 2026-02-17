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
 * Class AppointmentRescheduled
 *
 * @property string $RescheduleID
 * @property string $OldAppointmentID
 * @property string $NewAppointmentID
 * @property string|null $Reason
 * @property Carbon|null $CreatedOn
 * @property string|null $CreatedBy
 *
 * @package App\Models
 */
class AppointmentRescheduled extends Model
{
    protected $table = 'AppointmentRescheduled';
    protected $primaryKey = 'RescheduleID';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'CreatedOn' => 'datetime',
    ];

    protected $fillable = [
        'OldAppointmentID',
        'NewAppointmentID',
        'Reason',
        'CreatedOn',
        'CreatedBy',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->RescheduleID)) {
                $model->RescheduleID = (string) Str::uuid();
            }
            if (empty($model->CreatedOn)) {
                $model->CreatedOn = now();
            }
        });
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['RescheduleID'],
        );
    }

    public function oldAppointment()
    {
        return $this->belongsTo(Appointment::class, 'OldAppointmentID', 'AppointmentID');
    }

    public function newAppointment()
    {
        return $this->belongsTo(Appointment::class, 'NewAppointmentID', 'AppointmentID');
    }
}


