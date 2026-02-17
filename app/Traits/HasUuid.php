<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait for models that use UUID as primary key
 */
trait HasUuid
{
    /**
     * Boot the trait, adding a creating model event to set the UUID.
     */
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            // Ensure we have a primary key
            $key = $model->getKeyName();
            
            // Only set UUID if the primary key is empty
            if (empty($model->{$key})) {
                $model->{$key} = (string) Str::orderedUuid();
            }
        });
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }
}
