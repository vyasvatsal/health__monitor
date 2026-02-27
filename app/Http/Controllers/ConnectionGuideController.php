<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store;

class ConnectionGuideController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::orderBy('name')->get();

        // If they requested a specific store, or default to the first one
        $activeStoreId = $request->query('store_id');

        if ($activeStoreId) {
            $store = $stores->where('id', $activeStoreId)->first();
        } else {
            $store = $stores->first();
        }

        return view('settings.connection', compact('store', 'stores'));
    }
}
