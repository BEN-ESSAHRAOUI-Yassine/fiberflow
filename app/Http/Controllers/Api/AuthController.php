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

    /**
     * Register a new user account.
     *
     * Creates a new user and returns a Bearer token for immediate authentication.
     *
     * @group Authentication
     *
     * @unauthenticated
     *
     * @bodyParam name string required The user's full name. Example: Jean Dupont
     * @bodyParam email string required The user's email address. Example: jean@example.com
     * @bodyParam password string required The user's password. Must be confirmed. Example: secret-password
     * @bodyParam password_confirmation string required Must match the password field. Example: secret-password
     *
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "Jean Dupont",
     *     "email": "jean@example.com",
     *     "role": "ingenieur",
     *     "created_at": "2026-01-15T10:30:00.000000Z",
     *     "updated_at": "2026-01-15T10:30:00.000000Z"
     *   },
     *   "token": "1|abc123..."
     * }
     * @response 422 scenario="Validation failed" {"message": "The email has already been taken.", "errors": {"email": ["The email has already been taken."]}}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 201);
    }

    /**
     * Authenticate a user and obtain a Bearer token.
     *
     * @group Authentication
     *
     * @unauthenticated
     *
     * @bodyParam email string required The user's email address. Example: jean@example.com
     * @bodyParam password string required The user's password. Example: secret-password
     * @bodyParam remember boolean Remember me. Default: false. Example: true
     *
     * @response {
     *   "user": {
     *     "id": 1,
     *     "name": "Jean Dupont",
     *     "email": "jean@example.com",
     *     "role": "ingenieur",
     *     "created_at": "2026-01-15T10:30:00.000000Z",
     *     "updated_at": "2026-01-15T10:30:00.000000Z"
     *   },
     *   "token": "2|def456..."
     * }
     * @response 422 scenario="Invalid credentials" {"message": "These credentials do not match our records."}
     * @response 429 scenario="Too many attempts" {"message": "Too many login attempts. Please try again in 60 seconds."}
     */
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

    /**
     * Revoke the current Bearer token (logout).
     *
     * @group Authentication
     *
     * @response 204 scenario="Logged out successfully"
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @group Authentication
     *
     * @response {
     *   "id": 1,
     *   "name": "Jean Dupont",
     *   "email": "jean@example.com",
     *   "role": "ingenieur",
     *   "email_verified_at": null,
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T10:30:00.000000Z"
     * }
     */
    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update the authenticated user's password.
     *
     * @group Authentication
     *
     * @bodyParam current_password string required The current password. Example: old-password
     * @bodyParam new_password string required The new password. Must be confirmed. Example: new-password
     * @bodyParam new_password_confirmation string required Must match new_password. Example: new-password
     *
     * @response {"message": "Password updated successfully."}
     * @response 422 scenario="Validation failed" {"message": "The current password is incorrect.", "errors": {"current_password": ["The current password is incorrect."]}}
     */
    public function updatePassword(PasswordUpdateRequest $request): JsonResponse
    {
        $this->authService->updatePassword(
            $request->user(),
            $request->validated()['current_password'],
            $request->validated()['new_password'],
        );

        return response()->json(['message' => __('Password updated successfully.')]);
    }

    /**
     * Update the authenticated user's profile (name and email).
     *
     * @group Authentication
     *
     * @bodyParam name string required The user's full name. Example: Jean Dupont
     * @bodyParam email string required The user's email address. Example: jean@example.com
     *
     * @response {
     *   "user": {
     *     "id": 1,
     *     "name": "Jean Dupont",
     *     "email": "jean@example.com",
     *     "role": "ingenieur",
     *     "created_at": "2026-01-15T10:30:00.000000Z",
     *     "updated_at": "2026-01-15T10:30:00.000000Z"
     *   }
     * }
     */
    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->authService->updateProfile($request->user(), $request->validated());

        return response()->json(['user' => new UserResource($user)]);
    }

    /**
     * Delete the authenticated user's account.
     *
     * Permanently deletes the user and revokes all tokens. This action is irreversible.
     *
     * @group Authentication
     *
     * @bodyParam password string required The user's current password to confirm deletion. Example: secret-password
     *
     * @response {"message": "Account deleted successfully."}
     * @response 422 scenario="Wrong password" {"message": "The password is incorrect.", "errors": {"password": ["The password is incorrect."]}}
     */
    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        $this->authService->deleteAccount($request->user(), $request->validated()['password']);

        return response()->json(['message' => __('Account deleted successfully.')]);
    }
}
