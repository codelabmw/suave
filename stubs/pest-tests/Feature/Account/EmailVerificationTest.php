<?php

use App\Models\User;
use App\Models\VerificationCode;
use Codelabmw\Statuses\Http;
use Illuminate\Support\Facades\Event;

it('can send email verification code', function () {
    // Arrange
    $user = User::factory()->unverified()->create();

    Event::fake();

    // Act
    $response = $this->actingAs($user)->getJson('/v1/accounts/verification/email');

    // Assert
    $response->assertStatus(Http::NO_CONTENT);
    Event::assertDispatched('eloquent.created: ' . VerificationCode::class);
    expect(VerificationCode::count())->toBe(1);
});

it('cannot send email verification code if user is already verified', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)->getJson('/v1/accounts/verification/email');

    // Assert
    $response->assertStatus(Http::CONFLICT);
    expect(VerificationCode::count())->toBe(0);
});

it('cannot send email verification code if user is not authenticated', function () {
    // Act
    $response = $this->getJson('/v1/accounts/verification/email');

    // Assert
    $response->assertStatus(Http::UNAUTHORIZED);
    expect(VerificationCode::count())->toBe(0);
});

it('can verify email', function () {
    // Arrange
    $user = User::factory()->unverified()->create();
    $verificationCode = VerificationCode::factory()->create([
        'verifiable_id' => $user->id,
    ]);

    // Act
    $response = $this->actingAs($user)->postJson('/v1/accounts/verification/email', [
        'code' => $verificationCode->code,
    ]);

    // Assert
    $response->assertStatus(Http::NO_CONTENT);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    expect($verificationCode->fresh()->verified_at)->not->toBeNull();
});

it('cannot verify email if code is invalid', function () {
    // Arrange
    $user = User::factory()->unverified()->create();
    $verificationCode = VerificationCode::factory()->create([
        'verifiable_id' => $user->id,
    ]);

    // Act
    $response = $this->actingAs($user)->postJson('/v1/accounts/verification/email', [
        'code' => 123456,
    ]);

    // Assert
    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    expect($verificationCode->fresh()->verified_at)->toBeNull();
});

it('cannot verify email if code has expired', function () {
    // Arrange
    $user = User::factory()->unverified()->create();
    $verificationCode = VerificationCode::factory()->create([
        'verifiable_id' => $user->id,
        'expires_at' => now()->subMinutes(1),
    ]);

    // Act
    $response = $this->actingAs($user)->postJson('/v1/accounts/verification/email', [
        'code' => $verificationCode->code,
    ]);

    // Assert
    $response->assertStatus(Http::UNPROCESSABLE_CONTENT);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    expect($verificationCode->fresh()->verified_at)->toBeNull();
});

it('can guard routes against unverified users', function () {
    // Arrange
    $user = User::factory()->unverified()->create();

    // Act
    $response = $this->actingAs($user)->getJson('/v1/accounts/test');

    // Assert
    $response->assertStatus(Http::CONFLICT);
});