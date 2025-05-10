<?php

namespace App\Services\UserServices\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function updateUser(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            return $user->update($data);
        });
    }

    public function deleteUser(User $user)
    {
        return DB::transaction(function () use ($user) {
            return $user->delete();
        });
    }
}
