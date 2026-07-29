<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('api login returns token', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user', 'token']);
});

test('api invalid login returns error', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422);
});

test('api logout revokes token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/logout');

    $response->assertStatus(204);
    expect($user->tokens()->count())->toBe(0);
});

test('protected api routes return 401 without token', function () {
    $response = $this->getJson('/api/v1/projects');

    $response->assertUnauthorized();
});

test('protected api routes return 401 with invalid token', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token-abc')
        ->getJson('/api/v1/projects');

    $response->assertUnauthorized();
});

test('get api user returns authenticated user', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/user');

    $response->assertOk()
        ->assertJsonPath('data.name', 'Test User')
        ->assertJsonPath('data.email', $user->email);
});
