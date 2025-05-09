<?php

namespace App\Services\AuthServices;

use App\Models\User;

class AuthTokenService
{
    public function createToken(User $user)
    {
        $token = $user->createToken('access_token')->plainTextToken;

        return $token;
    }
}