<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = auth()->user()->tokens;
        return view('api-tokens.index', compact('tokens'));
    }

    public function create()
    {
        $token = auth()->user()->createToken('API Token');
        return back()->with('token', $token->plainTextToken);
    }

    public function destroy(PersonalAccessToken $token)
    {
        $token->delete();
        return back()->with('status', 'Token revoked successfully');
    }
}
