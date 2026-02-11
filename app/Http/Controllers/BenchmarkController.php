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
        $store = Store::where('user_id', auth()->id())->firstOrFail();

        $competitors = Competitor::where('store_id', $store->id)
            ->with([
                'results' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->get();

        return view('benchmarks.index', compact('competitors', 'store'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $store = Store::where('user_id', auth()->id())->firstOrFail();

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
