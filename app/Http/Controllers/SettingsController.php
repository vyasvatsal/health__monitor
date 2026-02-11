<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'google_api_key' => 'required|string',
        ]);

        $this->updateEnv(['GOOGLE_API_KEY' => $request->google_api_key]);

        return back()->with('success', 'Settings updated successfully.');
    }

    protected function updateEnv($data)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);

            foreach ($data as $key => $value) {
                // Wrap value in quotes if it contains spaces
                if (strpos($value, ' ') !== false) {
                    $value = '"' . $value . '"';
                }

                // Update existing key
                if (strpos($content, "$key=") !== false) {
                    $content = preg_replace("/^$key=.*/m", "$key=$value", $content);
                }
                // Add new key
                else {
                    $content .= "\n$key=$value";
                }
            }

            file_put_contents($path, $content);
        }
    }
}
