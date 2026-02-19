<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;

class IncidentController extends Controller
{
    public function index()
    {
        // Get incidents for stores owned by the user
        $incidents = Incident::whereHas('store', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy('created_at', 'desc')->get();

        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        return view('incidents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,investigating,identified,monitoring,resolved',
            'severity' => 'required|in:critical,major,minor,maintenance',
        ]);

        $store = \App\Models\Store::where('user_id', auth()->id())->firstOrFail();

        Incident::create([
            'store_id' => $store->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'severity' => $validated['severity'],
            // 'occurred_at' => now(), // managed by timestamps or separate column if needed
        ]);

        return redirect()->route('incidents.index')->with('success', 'Incident reported successfully.');
    }

    public function show(Incident $incident)
    {
        if ($incident->store->user_id !== auth()->id()) {
            abort(403);
        }
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        if ($incident->store->user_id !== auth()->id()) {
            abort(403);
        }
        return view('incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        if ($incident->store->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,investigating,identified,monitoring,resolved',
            'severity' => 'required|in:critical,major,minor,maintenance',
        ]);

        $data = $validated;

        if ($request->status === 'resolved' && $incident->status !== 'resolved') {
            $data['resolved_at'] = now();
        } elseif ($request->status !== 'resolved') {
            $data['resolved_at'] = null; // Un-resolve if status changes back
        }

        $incident->update($data);

        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully.');
    }

    public function destroy(Incident $incident)
    {
        if ($incident->store->user_id !== auth()->id()) {
            abort(403);
        }

        $incident->delete();

        return redirect()->route('incidents.index')->with('success', 'Incident deleted successfully.');
    }
}
