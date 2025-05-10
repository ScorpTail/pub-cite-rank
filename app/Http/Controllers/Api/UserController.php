<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Services\UserServices\UserService;
use App\Http\Requests\Api\User\UserRequest;
use App\Http\Requests\Api\User\UserUpdateAvatarRequest;
use App\Http\Resources\User\UserCabinetResource;

class UserController extends Controller
{
    public function __construct(public UserService $userService) {}

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return UserResource::make(auth()->user())->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request)
    {
        $this->userService->update(auth()->user(), $request->validated());

        return response()->json(['success' => __('front.success.cabinet.update')]);
    }

    public function cabinet()
    {
        return UserCabinetResource::make(auth()->user())->response();
    }

    public function updateAvatar(UserUpdateAvatarRequest $request)
    {
        $this->userService->updateAvatar(auth()->user(), $request->validated('file'));

        return response()->json(['success' => __('front.success.cabinet.update_avatar')]);
    }
}
