<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }
}
