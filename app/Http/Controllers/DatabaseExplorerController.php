<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class DatabaseExplorerController extends Controller
{
    public function index(Store $store)
    {
        // Ensure user owns the store
        if ($store->user_id !== auth()->id()) {
            abort(403);
        }

        $latestSchema = $store->databaseSchemas()->latest()->first();

        // Fetch all stores for the context switcher
        $allStores = Store::where('user_id', auth()->id())->get();

        return view('db_explorer', [
            'currentStore' => $store,
            'allStores' => $allStores,
            'latestSchema' => $latestSchema
        ]);
    }
}
