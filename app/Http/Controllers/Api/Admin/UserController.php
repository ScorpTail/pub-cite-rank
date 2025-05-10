<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserServices\UserService;
use App\Http\Requests\Api\Admin\User\UserRequest;
use App\Http\Resources\Admin\User\ShowUserResource;
use App\Http\Resources\Admin\User\IndexUserResource;

class UserController extends Controller
{
    public function __construct(
        public UserService $userService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return IndexUserResource::collection(
            $this->userService->user($request->query())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $this->userService->create($request->validated());

        return response()->json([
            'success' => __('admin.user.created'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $userId)
    {
        return ShowUserResource::make(
            $this->userService->user($request->query(), $userId)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $userId)
    {
        $user = $this->userService->user([], $userId);

        $this->userService->update($user, $request->validated());

        return response()->json([
            'success' => __('admin.user.updated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $userId)
    {
        $user = $this->userService->user([], $userId);

        $this->userService->delete($user);

        return response()->json([
            'success' => __('admin.user.deleted'),
        ]);
    }
}
