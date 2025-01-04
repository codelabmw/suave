<?php

namespace App\Http\Controllers\Account\Token;

use App\Models\User;
use App\Http\Controllers\Controller;

use Codelabmw\Statuses\Http;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class CreateTokenController extends Controller
{
    /**
     * Create a new token.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device' => 'required|string',
        ]);

        $user = User::firstWhere('email', $validated['email']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        $token = $user->createToken($validated['device'])->plainTextToken;

        return response()->json(['token' => $token], Http::OK);
    }
}