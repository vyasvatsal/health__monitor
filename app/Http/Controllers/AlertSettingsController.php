<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlertSettingsController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        // Default settings if null
        $settings = $user->settings ?? ['email_critical' => true, 'email_digest' => false];

        return view('settings.alerts', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email_critical' => 'boolean',
            'email_digest' => 'boolean',
        ]);

        // Explicitly handle "unchecked" checkboxes (which strictly aren't sent in POST)
        $settings = [
            'email_critical' => $request->has('email_critical'),
            'email_digest' => $request->has('email_digest'),
        ];

        $user = auth()->user();
        $user->settings = $settings;
        $user->save();

        return redirect()->back()->with('success', 'Alert preferences updated.');
    }
}
