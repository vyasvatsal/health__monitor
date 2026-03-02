<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\Store;
use App\Services\Benchmarking\BenchmarkRunner;
use Illuminate\Http\Request;

class BenchmarkController extends Controller
{
    public function index()
    {
        $stores = Store::where('user_id', auth()->id())->get();

        if ($stores->isEmpty()) {
            return redirect()->route('stores.create')->with('info', 'Please create a project to access Benchmarks.');
        }

        $competitors = Competitor::whereIn('store_id', $stores->pluck('id'))
            ->with([
                'store',
                'results' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->get();

        return view('benchmarks.index', compact('competitors', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $store = Store::where('id', $request->store_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Competitor::create([
            'store_id' => $store->id,
            'name' => $request->name,
            'url' => $request->url,
        ]);

        return redirect()->route('benchmarks.index')->with('success', 'Competitor added.');
    }

    public function scan(Competitor $competitor, BenchmarkRunner $runner)
    {
        // Security check
        if ($competitor->store->user_id !== auth()->id()) {
            abort(403);
        }

        $result = $runner->run($competitor->store, $competitor);

        return redirect()->route('benchmarks.index')->with('success', 'Benchmark complete! Winner: ' . strtoupper($result->winner));
    }

    public function destroy(Competitor $competitor)
    {
        if ($competitor->store->user_id !== auth()->id()) {
            abort(403);
        }

        $competitor->delete();

        return redirect()->route('benchmarks.index')->with('success', 'Competitor removed.');
    }
}
