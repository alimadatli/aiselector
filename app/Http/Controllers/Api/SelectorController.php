<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\Selector;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SelectorController extends Controller
{
    public function index(Website $website): JsonResponse
    {
        return response()->json($website->selectors);
    }

    public function store(Request $request, Website $website): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'selector' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $selector = $website->selectors()->create($validated);
        return response()->json($selector, 201);
    }

    public function show(Website $website, Selector $selector): JsonResponse
    {
        return response()->json($selector);
    }

    public function update(Request $request, Website $website, Selector $selector): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'selector' => 'string|max:255',
            'description' => 'string',
            'is_active' => 'boolean'
        ]);

        $selector->update($validated);
        return response()->json($selector);
    }

    public function destroy(Website $website, Selector $selector): JsonResponse
    {
        $selector->delete();
        return response()->json(null, 204);
    }

    public function changes(Website $website, Selector $selector): JsonResponse
    {
        $changes = $selector->changes()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($changes);
    }
}
