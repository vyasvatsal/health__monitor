<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store;

class ConnectionGuideController extends Controller
{
    public function index()
    {
        // For simplicity, we grab the first store since it's a single-user dashboard setup
        $store = Store::first();

        return view('settings.connection', compact('store'));
    }
}
