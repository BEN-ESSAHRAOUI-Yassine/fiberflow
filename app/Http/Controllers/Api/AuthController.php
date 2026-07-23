<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DeleteAccountRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function updatePassword(PasswordUpdateRequest $request): JsonResponse
    {
        $this->authService->updatePassword(
            $request->user(),
            $request->validated()['current_password'],
            $request->validated()['new_password'],
        );

        return response()->json(['message' => __('Password updated successfully.')]);
    }

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->authService->updateProfile($request->user(), $request->validated());

        return response()->json(['user' => new UserResource($user)]);
    }

    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        $this->authService->deleteAccount($request->user(), $request->validated()['password']);

        return response()->json(['message' => __('Account deleted successfully.')]);
    }
}
