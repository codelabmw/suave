<?php

use App\Models\User;
use Codelabmw\Statuses\Http;
use Illuminate\Support\Facades\DB;

it('can create a token when user logs in', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->postJson('/v1/accounts/tokens', [
        'email' => $user->email,
        'password' => 'password',
        'device' => 'Test Device',
    ]);

    // Assert
    $response->assertStatus(Http::OK);
    $response->assertJsonStructure(['token']);
    expect(DB::table('personal_access_tokens')->count())->toBe(1);
});

it('cannot create a token when user logs in with invalid credentials', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->postJson('/v1/accounts/tokens', [
        'email' => $user->email,
        'password' => 'invalid-password',
        'device' => 'Test Device',
    ]);

    // Assert
    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    $response->assertJsonValidationErrors(['email']);
    expect(DB::table('personal_access_tokens')->count())->toBe(0);
});

it('can delete a token when user logs out', function () {
    // Arrange
    $user = User::factory()->create();
    $token = $user->createToken('Test Device')->plainTextToken;

    // Act
    $response = $this->deleteJson('/v1/accounts/tokens', [], [
        'Authorization' => "Bearer $token",
    ]);

    // Assert
    $response->assertStatus(Http::NO_CONTENT);
    expect(DB::table('personal_access_tokens')->count())->toBe(0);
});
