<?php

use App\Models\User;
use Codelabmw\Statuses\Http;

it('can create a session when user logs in', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/v1/accounts/sessions', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(Http::NO_CONTENT);
    $this->assertAuthenticated();
});

it('cannot create a session when user logs in with invalid credentials', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/v1/accounts/sessions', [
        'email' => $user->email,
        'password' => 'invalid-password',
    ]);

    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    $this->assertGuest();
});

it('can delete a session when user logs out', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/v1/accounts/sessions');

    $response->assertStatus(Http::NO_CONTENT);
    $this->assertGuest();
});
