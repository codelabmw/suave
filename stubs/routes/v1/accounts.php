<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\EmailVerificationController;
use App\Http\Controllers\Account\ForgotPasswordController;
use App\Http\Controllers\Account\Session\CreateSessionController;
use App\Http\Controllers\Account\Session\DeleteSessionController;
use App\Http\Controllers\Account\Token\CreateTokenController;
use App\Http\Controllers\Account\Token\DeleteTokenController;
use Illuminate\Support\Facades\Route;

/*
|------------------------------------------
| Accounts Endpoint
|------------------------------------------
*/
Route::prefix('accounts')->group(function () {
    // Create a new account / user
    Route::post('/', [AccountController::class, 'store'])->middleware('guest');

    // Get the currently authenticated user
    Route::get('/', [AccountController::class, 'show'])->middleware(['auth:sanctum']);

    // Email verification
    Route::middleware(['auth:sanctum'])->group(function () {
        // Send email verification code
        Route::get('/verification/email', [EmailVerificationController::class, 'send']);

        // Verify email
        Route::post('/verification/email', [EmailVerificationController::class, 'verify']);
    });

    // Forgot password
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendTemporaryPassword'])->middleware('guest');

    // Reset password
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('auth:sanctum');
});

/*
|------------------------------------------
| Sessions Endpoint
|------------------------------------------
*/
Route::withoutMiddleware('api')->middleware('web')->prefix('accounts/sessions')->group(function () {
    // Create a new session / login
    Route::post('/', CreateSessionController::class)->middleware('guest');

    // Delete a session / logout
    Route::delete('/', DeleteSessionController::class)->middleware('auth');
});

/*
|------------------------------------------
| Tokens Endpoint
|------------------------------------------
*/
Route::prefix('accounts/tokens')->group(function () {
    // Create a new token
    Route::post('/', CreateTokenController::class)->middleware('guest');

    // Delete a token
    Route::delete('/', DeleteTokenController::class)->middleware('auth:sanctum');
});

/*
|------------------------------------------
| Used For Tests
|------------------------------------------
| If this route is deleted you might have to adjust tests that are using this route to avoid
| failing tests.
|
| Tests that are using this route:
| - tests/Feature/Account/EmailVerificationTest.php
| - tests/Feature/Account/ForgotPasswordTest.php
*/
Route::get('/accounts/test', function () {
    return response()->json(request()->user());
})->middleware(['auth:sanctum', 'verified', 'password.reset']);
