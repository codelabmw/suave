<?php

namespace App\Http\Controllers\Account\Session;

use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DeleteSessionController extends Controller
{
    /**
     * Delete a session / logout
     */
    public function __invoke(Request $request): Response
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}