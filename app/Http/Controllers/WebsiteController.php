<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function showSelectors(Website $website)
    {
        return view('websites.selectors', compact('website'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        $website = Website::create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'is_active' => true,
            'user_id' => auth()->id(),
        ]);

        return response()->json($website, 201);
    }

    public function update(Request $request, Website $website)
    {
        if ($website->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|url|max:255',
            'is_active' => 'sometimes|required|boolean',
        ]);

        $website->update($validated);

        return response()->json($website);
    }

    public function destroy(Website $website)
    {
        if ($website->user_id !== auth()->id()) {
            abort(403);
        }

        $website->delete();

        return response()->json(null, 204);
    }
}
