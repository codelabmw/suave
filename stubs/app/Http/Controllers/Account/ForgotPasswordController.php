<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Send a temporary password to the user.
     */
    public function sendTemporaryPassword(Request $request): Response
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::firstWhere('email', $request->email);
        $user->sendTemporaryPasswordNotification();

        return response()->noContent();
    }

    /**
     * Reset the password of the authenticated user.
     */
    public function resetPassword(Request $request): Response
    {
        $request->validate([
            'password' => ['required', Password::default(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'must_reset_password' => false,
        ]);

        return response()->noContent();
    }
}