<?php

use App\Models\User;

test('profile page can be displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/profile');

    $user->refresh();

    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email cannot be changed to an existing email', function () {
    $existing = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => 'existing@example.com',
        ]);

    $response->assertSessionHasErrors('email');
});

test('api profile can be updated', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->putJson('/api/v1/profile', [
            'name' => 'API Updated',
            'email' => 'api@example.com',
        ]);

    $response->assertOk();
    expect($user->fresh()->name)->toBe('API Updated');
});
