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

    public function show(Incident $incident)
    {
        if ($incident->store->user_id !== auth()->id()) {
            abort(403);
        }
        return view('incidents.show', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        if ($incident->store->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:open,investigating,resolved',
        ]);

        $data = ['status' => $request->status];
        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $incident->update($data);

        return redirect()->route('incidents.show', $incident)->with('success', 'Incident updated.');
    }
}
