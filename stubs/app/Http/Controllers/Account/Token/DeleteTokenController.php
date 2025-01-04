<?php

namespace App\Http\Controllers\Account\Token;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DeleteTokenController extends Controller
{
    /**
     * Delete a token.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'all' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $user->currentAccessToken()->delete();

        if ($request->all) {
            $user->tokens()->delete();
        }

        return response()->noContent();
    }
}