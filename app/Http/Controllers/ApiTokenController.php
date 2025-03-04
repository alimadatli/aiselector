<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = auth()->user()->tokens;
        return view('api-tokens.index', compact('tokens'));
    }

    public function create(Request $request)
    {
        $token = auth()->user()->createToken('API Token');
        return back()->with('token', $token->plainTextToken);
    }

    public function destroy(PersonalAccessToken $token)
    {
        if ($token->tokenable_id !== auth()->id()) {
            abort(403);
        }
        $token->delete();
        return back()->with('status', 'Token revoked successfully');
    }
}
