<?php

use App\Enums\UserRole;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->engineer = User::factory()->create(['role' => UserRole::Ingenieur]);
});

describe('GET /api/v1/users', function () {

    it('returns paginated users for admin', function () {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/users');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('returns 403 for engineer', function () {
        $response = $this->actingAs($this->engineer)
            ->getJson('/api/v1/users');

        $response->assertForbidden();
    });

    it('returns 401 for guest', function () {
        $response = $this->getJson('/api/v1/users');

        $response->assertUnauthorized();
    });
});

describe('POST /api/v1/users', function () {

    it('creates user for admin', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/users', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password123',
                'role' => 'ingenieur',
            ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']]);

        expect(User::where('email', 'new@example.com')->exists())->toBeTrue();
    });

    it('returns 403 for engineer', function () {
        $response = $this->actingAs($this->engineer)
            ->postJson('/api/v1/users', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password123',
                'role' => 'ingenieur',
            ]);

        $response->assertForbidden();
    });
});

describe('GET /api/v1/users/{id}', function () {

    it('returns user for admin', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/users/{$user->id}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']]);
    });

    it('returns 403 for engineer', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($this->engineer)
            ->getJson("/api/v1/users/{$user->id}");

        $response->assertForbidden();
    });
});

describe('PUT /api/v1/users/{id}', function () {

    it('updates user for admin', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/users/{$user->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertOk();
        expect($user->fresh()->name)->toBe('Updated Name');
    });

    it('returns 403 for engineer', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($this->engineer)
            ->putJson("/api/v1/users/{$user->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertForbidden();
    });
});

describe('DELETE /api/v1/users/{id}', function () {

    it('soft-deletes user for admin', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/v1/users/{$user->id}");

        $response->assertNoContent();
        expect($user->fresh()->trashed())->toBeTrue();
    });

    it('returns 403 for engineer', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($this->engineer)
            ->deleteJson("/api/v1/users/{$user->id}");

        $response->assertForbidden();
    });
});

describe('Archived user login', function () {

    it('cannot log in after soft deletion', function () {
        $user = User::factory()->create([
            'email' => 'archived@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->delete();

        $response = $this->postJson('/api/v1/login', [
            'email' => 'archived@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    });
});
