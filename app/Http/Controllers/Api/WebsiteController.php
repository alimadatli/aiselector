<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WebsiteController extends Controller
{
    public function index(): JsonResponse
    {
        $websites = Website::with('selectors')->get();
        return response()->json($websites);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'is_active' => 'boolean'
        ]);

        $website = Website::create($validated);
        return response()->json($website, 201);
    }

    public function show(Website $website): JsonResponse
    {
        return response()->json($website->load('selectors'));
    }

    public function update(Request $request, Website $website): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'url' => 'url|max:255',
            'is_active' => 'boolean'
        ]);

        $website->update($validated);
        return response()->json($website);
    }

    public function destroy(Website $website): JsonResponse
    {
        $website->delete();
        return response()->json(null, 204);
    }
}
