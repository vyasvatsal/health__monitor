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
            'gemini_api_key_chatbot' => 'nullable|string',
            'monthly_revenue' => 'nullable|numeric',
            'slack_webhook_url' => 'nullable|url',
        ]);

        $data = [];
        if ($request->has('gemini_api_key_chatbot'))
            $data['GEMINI_API_KEY_CHATBOT'] = $request->gemini_api_key_chatbot;
        if ($request->has('monthly_revenue'))
            $data['MONTHLY_REVENUE'] = $request->monthly_revenue;
        if ($request->has('slack_webhook_url'))
            $data['SLACK_WEBHOOK_URL'] = $request->slack_webhook_url;

        $this->updateEnv($data);

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
