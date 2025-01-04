<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use App\Events\UserCreatedEvent;
use App\Http\Controllers\Controller;

use Codelabmw\Statuses\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Create a new account.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        defer(fn() => event(new UserCreatedEvent($user)));

        return response()->json($user, Http::CREATED);
    }

    /**
     * Get the currently authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}