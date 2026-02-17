<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">
            {{ __('Global Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col">
        <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- AI Configuration -->
            <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                <div class="p-6 border-b border-white/5 bg-slate-900/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="text-emerald-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </span>
                            AI Configuration
                        </h3>
                        <p class="text-sm text-slate-400 mt-1">Configure Google Gemini to enable autonomous insights.
                        </p>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        <div class="mb-6">
                            <label for="gemini_api_key_chatbot"
                                class="block text-sm font-medium text-slate-300 mb-2">xAI (Grok) API Key</label>
                            <div class="flex gap-2">
                                <input id="gemini_api_key_chatbot" type="password" name="gemini_api_key_chatbot"
                                    value="{{ old('gemini_api_key_chatbot', config('services.xai.key')) }}"
                                    class="block w-full rounded-lg bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2.5"
                                    placeholder="gsk_...">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Get your API key from <a href="https://console.x.ai/" target="_blank"
                                    class="text-emerald-400 hover:underline">xAI Console</a>.
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="monthly_revenue" class="block text-sm font-medium text-slate-300 mb-2">Monthly
                                Revenue (USD)</label>
                            <div class="flex gap-2">
                                <input id="monthly_revenue" type="number" name="monthly_revenue"
                                    value="{{ old('monthly_revenue', env('MONTHLY_REVENUE', 50000)) }}"
                                    class="block w-full rounded-lg bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2.5"
                                    placeholder="50000">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Used to calculate predictive revenue loss.
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="slack_webhook_url" class="block text-sm font-medium text-slate-300 mb-2">Slack
                                Webhook URL</label>
                            <div class="flex gap-2">
                                <input id="slack_webhook_url" type="url" name="slack_webhook_url"
                                    value="{{ old('slack_webhook_url', env('SLACK_WEBHOOK_URL')) }}"
                                    class="block w-full rounded-lg bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2.5"
                                    placeholder="https://hooks.slack.com/services/...">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Receive critical alerts and daily digests in your Slack channel.
                            </p>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-lg transition-colors shadow-lg shadow-emerald-500/20">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SaaS & Tiers (Placeholder) -->
            <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 opacity-75">
                <div class="p-6 border-b border-white/5 bg-slate-900/50">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        SaaS Configuration
                    </h3>
                    <p class="text-sm text-slate-400 mt-1">Manage subscription tiers and limits (Coming Soon).</p>
                </div>
                <div class="p-6 text-center text-slate-500 text-sm">
                    Configure plans and limits in the next update.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>