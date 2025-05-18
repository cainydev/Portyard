<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait IsCreatedByUser
{
    public static function bootIsCreatedByUser(): void
    {
        static::creating(/**
         * @throws Exception
         */ function (Model $model) {
            if (!auth()->check()) throw new Exception('Can\'t create ' . $model::class . ' when logged out.');

            $model->user_id = auth()->user()->id;
        });
    }
}
