<?php

use App\Models\User;
use Codelabmw\Statuses\Http;
use Illuminate\Support\Facades\Hash;

it('can send a temporary password to the user', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/v1/accounts/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertStatus(Http::NO_CONTENT);
    expect(Hash::check('password', $user->fresh()->password))->not->toBeTrue();
});

it('can not send a temporary password when the email is invalid', function () {
    $response = $this->postJson('/v1/accounts/forgot-password', [
        'email' => 'invalid-email',
    ]);

    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    $response->assertJsonValidationErrors('email');
});

it('can not send a temporary password when the email does not exist', function () {
    $response = $this->postJson('/v1/accounts/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    $response->assertJsonValidationErrors('email');
});

it('can guard routes against users that must reset their password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_reset_password' => true,
    ]);

    $response = $this->actingAs($user)->get('/v1/accounts/test');

    $response->assertStatus(Http::CONFLICT);
    $response->assertSee('You must reset your password before proceeding.');
});

it('can reset the password of a user', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_reset_password' => true,
    ]);

    $response = $this->actingAs($user)->postJson('/v1/accounts/reset-password', [
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertStatus(Http::NO_CONTENT);
    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

it('can not reset the password of a user when the password is invalid', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_reset_password' => true,
    ]);

    $response = $this->actingAs($user)->postJson('/v1/accounts/reset-password', [
        'password' => 'passw',
        'password_confirmation' => 'passw',
    ]);

    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    $response->assertJsonValidationErrors('password');
});
