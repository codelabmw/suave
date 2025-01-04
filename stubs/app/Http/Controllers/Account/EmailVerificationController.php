<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Codelabmw\Statuses\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    /**
     * Send email verification code.
     */
    public function send(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(status: Http::CONFLICT);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(status: Http::NO_CONTENT);
    }

    /**
     * Verify email.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'numeric'],
        ]);

        $user = $request->user();
        $verificationCode = $user->verificationCodes()->latest()->first();

        if ($verificationCode === null || $verificationCode->code != $request->code || $verificationCode->hasExpired()) {
            throw ValidationException::withMessages(['code' => 'The provided code is invalid.']);
        }

        $verificationCode->update(['verified_at' => now()]);
        $user->markEmailAsVerified();

        return response()->json(status: Http::NO_CONTENT);
    }
}
