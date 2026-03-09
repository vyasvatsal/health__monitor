<?php

namespace App\Http\Controllers;

use App\Models\StoreAlert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function markAsRead(StoreAlert $alert)
    {
        // Ensure user owns the store the alert belongs to
        if ($alert->store->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $alert->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
