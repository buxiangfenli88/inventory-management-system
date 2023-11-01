<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class CustomerPolicy
{
    public function update(User $user)
    {
        return $user->hasAnyRole([UserRole::ADMIN]);
    }
}