<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait Blameable
{
    protected static function bootBlameable()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::ulid();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
