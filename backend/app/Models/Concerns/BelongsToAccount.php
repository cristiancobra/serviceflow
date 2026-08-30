<?php

namespace App\Models\Concerns;

use App\Models\Scopes\AccountScope;

trait BelongsToAccount
{
    public static function bootBelongsToAccount()
    {
        static::addGlobalScope(new AccountScope);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;
            }
        });
    }
}
