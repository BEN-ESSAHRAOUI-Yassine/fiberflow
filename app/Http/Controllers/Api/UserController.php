<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * List all users (admin only).
     *
     * @group Users
     *
     * Returns a paginated collection of all users in the system.
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Jean Dupont",
     *       "email": "jean@example.com",
     *       "role": "ingenieur",
     *       "email_verified_at": null,
     *       "created_at": "2026-01-15T10:30:00.000000Z",
     *       "updated_at": "2026-01-15T10:30:00.000000Z"
     *     }
     *   ]
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return new UserCollection($this->userService->list());
    }

    /**
     * Create a new user (admin only).
     *
     * @group Users
     *
     * @bodyParam name string required User's full name. Max 100 chars. Example: Jean Dupont
     * @bodyParam email string required User's email address. Unique. Example: jean@example.com
     * @bodyParam password string required User's password. Min 8 chars. Must meet password rules. Example: secret-password
     * @bodyParam role string required User role. Enum: admin, ingenieur. Example: ingenieur
     *
     * @response 201 {
     *   "id": 2,
     *   "name": "Jean Dupont",
     *   "email": "jean@example.com",
     *   "role": "ingenieur",
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T10:30:00.000000Z"
     * }
     * @response 422 scenario="Validation failed" {"message": "The email has already been taken.", "errors": {"email": ["The email has already been taken."]}}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = $this->userService->create($request->validated());

        return new UserResource($user);
    }

    /**
     * Get a specific user by ID (admin only).
     *
     * @group Users
     *
     * @urlParam user integer required The user ID. Example: 1
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
     * @response 404 scenario="Not found" {"message": "User not found."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update a user (admin only).
     *
     * @group Users
     *
     * @urlParam user integer required The user ID. Example: 1
     *
     * @bodyParam name string Optional User's full name. Max 100 chars. Example: Jean Dupont Updated
     * @bodyParam email string Optional User's email address. Unique. Example: jean.updated@example.com
     * @bodyParam password string Optional User's new password. Min 8 chars. Example: new-password
     * @bodyParam role string Optional User role. Enum: admin, ingenieur. Example: admin
     *
     * @response {
     *   "id": 1,
     *   "name": "Jean Dupont Updated",
     *   "email": "jean.updated@example.com",
     *   "role": "admin",
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T12:00:00.000000Z"
     * }
     * @response 404 scenario="Not found" {"message": "User not found."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user = $this->userService->update($user, $request->validated());

        return new UserResource($user);
    }

    /**
     * Delete a user (admin only).
     *
     * Permanently deletes the user account.
     *
     * @group Users
     *
     * @urlParam user integer required The user ID. Example: 1
     *
     * @response 204 scenario="Deleted successfully"
     * @response 404 scenario="Not found" {"message": "User not found."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return response()->noContent();
    }
}
