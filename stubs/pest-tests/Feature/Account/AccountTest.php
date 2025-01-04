<?php

use App\Events\UserCreatedEvent;
use App\Models\User;
use Codelabmw\Statuses\Http;
use Illuminate\Support\Facades\Event;

it('can create an account', function () {
    // Arrange
    $name = 'Test User';
    $email = 'test@example.com';
    $password = 'password';

    // Act
    $response = $this->postJson('v1/accounts', [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password,
    ]);

    // Assert
    $response->assertStatus(Http::CREATED);
    expect(User::count())->toBe(1);
    expect(User::first()->email)->toBe($email);
});

it('cannot create an account with an existing email', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->postJson('v1/accounts', [
        'name' => 'Test User',
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Assert
    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    expect(User::count())->toBe(1);
});

it('dispatches a user created event', function () {
    // Arrange
    $name = 'Test User';
    $email = 'test@example.com';
    $password = 'password';

    Event::fake();

    // Act
    $response = $this->postJson('v1/accounts', [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password,
    ]);

    // Assert
    $response->assertStatus(Http::CREATED);
    Event::assertDispatched(UserCreatedEvent::class);
});

it('can get currently authenticated user', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)->getJson('v1/accounts');

    // Assert
    $response->assertStatus(Http::OK);
    $response->assertJsonFragment(['email' => $user->email]);
});
