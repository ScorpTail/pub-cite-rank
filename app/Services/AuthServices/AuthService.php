<?php

namespace App\Services\AuthServices;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function register(array $regData)
    {
        $data = $this->prepareData($regData);

        return $this->authenticateUser($this->createUser($data));
    }

    public function login(array $loginData)
    {
        return Auth::once($loginData) ? $this->authenticateUser(auth()->user()) : false;
    }

    public function logout()
    {
        PersonalAccessToken::findToken(request()->bearerToken())?->delete();
    }

    private function prepareData(array $data)
    {
        return $data;
    }

    private function authenticateUser(User $user)
    {
        return [$user, $this->generateToken($user)];
    }

    private function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);

            return $user;
        });
    }

    private function generateToken(User $user)
    {
        $existingToken = request()->bearerToken();

        $token = PersonalAccessToken::findToken($existingToken);

        if (!$existingToken || !$token) {
            return app(AuthTokenService::class)->createToken($user);
        }

        return $existingToken;
    }
}
