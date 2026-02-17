<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class DeveloperController extends Controller
{
    public function index()
    {
        // Get the current store context (assuming single store or first store context for now)
        // ideally this should be selected via query param or session, similar to dashboard
        $store = Store::where('user_id', auth()->id())->firstOrFail();

        return view('settings.developer', compact('store'));
    }
}
