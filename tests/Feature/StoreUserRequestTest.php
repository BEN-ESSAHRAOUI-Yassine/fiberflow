<?php

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

describe('StoreUserRequest validation', function () {

    it('passes with valid data', function () {
        $rules = (new StoreUserRequest)->rules();

        $validator = Validator::make([
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'ingenieur',
        ], $rules);

        expect($validator->passes())->toBeTrue();
    });

    it('fails when name is missing', function () {
        $rules = (new StoreUserRequest)->rules();

        $validator = Validator::make([
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'ingenieur',
        ], $rules);

        expect($validator->fails())->toBeTrue();
    });

    it('fails when email is invalid', function () {
        $rules = (new StoreUserRequest)->rules();

        $validator = Validator::make([
            'name' => 'New User',
            'email' => 'not-an-email',
            'password' => 'password123',
            'role' => 'ingenieur',
        ], $rules);

        expect($validator->fails())->toBeTrue();
    });

    it('fails when role is invalid', function () {
        $rules = (new StoreUserRequest)->rules();

        $validator = Validator::make([
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'superadmin',
        ], $rules);

        expect($validator->fails())->toBeTrue();
    });

    it('fails when password is too short', function () {
        $rules = (new StoreUserRequest)->rules();

        $validator = Validator::make([
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'short',
            'role' => 'ingenieur',
        ], $rules);

        expect($validator->fails())->toBeTrue();
    });
});

describe('UpdateUserRequest validation', function () {

    it('passes with partial data', function () {
        $rules = (new UpdateUserRequest)->rules();

        $validator = Validator::make([
            'name' => 'Updated Name',
        ], $rules);

        expect($validator->passes())->toBeTrue();
    });

    it('fails with invalid role', function () {
        $rules = (new UpdateUserRequest)->rules();

        $validator = Validator::make([
            'role' => 'superadmin',
        ], $rules);

        expect($validator->fails())->toBeTrue();
    });

    it('fails with invalid email format', function () {
        $rules = (new UpdateUserRequest)->rules();

        $validator = Validator::make([
            'email' => 'not-email',
        ], $rules);

        expect($validator->fails())->toBeTrue();
    });
});

describe('UserResource', function () {

    it('returns correct JSON structure', function () {
        $user = User::factory()->create();
        $resource = (new UserResource($user))->response()->getData(true);

        expect($resource['data'])->toHaveKeys(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at']);
    });
});

describe('UserCollection', function () {

    it('returns paginated collection', function () {
        User::factory()->count(3)->create();
        $users = User::paginate(10);
        $collection = (new UserCollection($users))->response()->getData(true);

        expect($collection['data'])->toBeArray();
    });
});
