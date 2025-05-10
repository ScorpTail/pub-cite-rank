<?php

namespace App\Services\UserServices;

use App\Models\User;
use App\Models\Image;
use App\Services\UserServices\Repositories\UserRepository;

class UserService
{
    public function __construct(
        public UserRepository $userRepository,
    ) {}

    public function user(array $param = [], ?int $userId = null)
    {
        return $userId
            ? $this->userRepository->getUser($userId, $param)
            : $this->userRepository->getUsers($param);
    }

    public function create(array $data)
    {
        $user = $this->userRepository->createUser($data);

        return $user;
    }

    public function update(User $user, array $data)
    {
        $this->userRepository->updateUser($user, $data);

        return $user;
    }

    public function delete(User $user)
    {
        $this->userRepository->deleteUser($user);
    }

    public function updateAvatar(User $user, $avatar)
    {
        if ($user->mainImage) {
            Image::deleteImage($user->mainImage);
        }

        Image::storeImage($avatar, $user->id, User::class, 'main');

        return true;
    }
}
