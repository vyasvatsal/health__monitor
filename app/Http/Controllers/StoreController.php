<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::where('user_id', auth()->id())
            ->withCount(['healthChecks', 'incidents'])
            ->with([
                'healthChecks' => function ($q) {
                    $q->latest()->limit(1); // For status
                }
            ])
            ->get();

        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);

        // Check Project Limit
        if (auth()->user()->stores()->count() >= auth()->user()->max_projects) {
            return back()->with('error', 'Project limit reached. Upgrade to Pro for unlimited projects.');
        }

        $store = Store::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'domain' => $request->domain,
            'api_key' => 'live_sk_' . Str::random(24),
            'tier' => 'basic',
        ]);

        return redirect()->route('stores.show', $store)->with('success', 'Project created successfully.');
    }

    public function show(Store $store)
    {
        if ($store->user_id !== auth()->id()) {
            abort(403);
        }
        return view('stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        if ($store->user_id !== auth()->id()) {
            abort(403);
        }
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        if ($store->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);

        $store->update($request->only('name', 'domain'));

        return redirect()->route('stores.show', $store)->with('success', 'Project updated successfully.');
    }
}
