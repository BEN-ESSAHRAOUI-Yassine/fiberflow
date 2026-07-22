<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/profile');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('current password must be correct to update password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

    $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
});

test('new password must match confirmation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'mismatch',
        ]);

    $response->assertSessionHasErrors('new_password');
});

test('api password can be updated', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->putJson('/api/password', [
            'current_password' => 'password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

    $response->assertOk();
    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
