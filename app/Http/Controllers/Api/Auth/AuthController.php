<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResetRequest;
use App\Services\AuthServices\AuthService;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\AuthServices\AuthResetPasswordService;

class AuthController extends Controller
{
    public function __construct(
        public AuthService $authService,
        public AuthResetPasswordService $authResetPasswordService,
    ) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        [$user, $token] = $this->authService->register($data);

        return response()->json([
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        [$user, $token] = $this->authService->login($data);

        $response = $token
            ? ['token' => $token]
            : ['error' => __('front.auth.failed')];

        return response()->json($response, $token ? 200 : 401);
    }

    public function sendResetLink(ResetRequest $request)
    {
        $result = $this->authResetPasswordService->sendResetLink($request->email);

        return response()->json($result
            ? [
                'success' => __('front.auth.password_reset_link_sent'),
            ]
            : [
                'message' => __('front.auth.password_reset_link_failed'),
            ], $result ? 200 : 400);
    }

    public function resetPassword(ResetRequest $request)
    {
        $data = $request->validated();

        $result = $this->authResetPasswordService->resetPassword($data);

        return response()->json($result
            ? [
                'success' => __('front.auth.password_reset_success'),
            ]
            : [
                'message' => __('front.auth.password_reset_failed'),
            ], $result ? 200 : 400);
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'success' => __('front.auth.logout_success'),
        ]);
    }
}
