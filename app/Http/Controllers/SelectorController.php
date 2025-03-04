<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Selector;
use Illuminate\Http\Request;

class SelectorController extends Controller
{
    public function index(Website $website)
    {
        if ($website->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json($website->selectors);
    }

    public function store(Request $request, Website $website)
    {
        if ($website->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'selector' => 'required|string|max:255',
            'type' => 'required|string|in:text,link,image',
            'is_active' => 'required|boolean',
        ]);

        $selector = $website->selectors()->create([
            'name' => $validated['name'],
            'selector' => $validated['selector'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
        ]);

        return response()->json($selector, 201);
    }

    public function update(Request $request, Website $website, Selector $selector)
    {
        if ($website->user_id !== auth()->id()) {
            abort(403);
        }

        if ($selector->website_id !== $website->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'selector' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|in:text,link,image',
            'is_active' => 'sometimes|required|boolean',
        ]);

        $selector->update($validated);

        return response()->json($selector);
    }

    public function destroy(Website $website, Selector $selector)
    {
        if ($website->user_id !== auth()->id()) {
            abort(403);
        }

        if ($selector->website_id !== $website->id) {
            abort(404);
        }

        $selector->delete();

        return response()->json(null, 204);
    }
}
