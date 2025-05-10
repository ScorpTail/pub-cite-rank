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

    public function update(array $data)
    {
        $user = auth()->user();

        $this->userRepository->updateUser($user, $data);

        return $user;
    }

    public function delete()
    {
        $user = auth()->user();

        $this->userRepository->deleteUser($user);
    }

    public function updateAvatar($avatar)
    {
        $user = auth()->user();

        if ($user->mainImage) {
            Image::deleteImage($user->mainImage);
        }

        Image::storeImage($avatar, $user->id, User::class, 'main');

        return true;
    }
}
