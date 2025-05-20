<?php

namespace App\Services\UserServices\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function getUsers(array $param = [])
    {
        $query = User::query();

        if (isset($param['name'])) {
            $query->where('first_name', 'like', '%' . $param['name'] . '%')
                ->orWhere('middle_name', 'like', '%' . $param['name'] . '%')
                ->orWhere('last_name', 'like', '%' . $param['name'] . '%');
        }

        return $query->paginate(15);
    }

    public function getUser(int $userId, array $param = [])
    {
        $query = User::query();

        $query->where('id', $userId);

        return $query->first();
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            return User::create($data);
        });
    }

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
