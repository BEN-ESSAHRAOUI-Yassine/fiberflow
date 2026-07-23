<?php

use App\Enums\UserRole;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->engineer = User::factory()->create(['role' => UserRole::Ingenieur]);
});

describe('EnsureUserIsAdmin middleware', function () {

    it('allows admin to access admin-protected route', function () {
        Route::get('/_test/admin-only', fn () => 'ok')->middleware('admin');

        $this->actingAs($this->admin)
            ->get('/_test/admin-only')
            ->assertOk();
    });

    it('returns 403 for engineer on admin-protected route', function () {
        Route::get('/_test/admin-only', fn () => 'ok')->middleware('admin');

        $this->actingAs($this->engineer)
            ->get('/_test/admin-only')
            ->assertForbidden();
    });

    it('returns 403 for guest on admin-protected route', function () {
        Route::get('/_test/admin-only', fn () => 'ok')->middleware('admin');

        $this->get('/_test/admin-only')
            ->assertForbidden();
    });
});
