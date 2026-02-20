<x-settings-layout>
    <div class="space-y-6">
        <div class="bg-[#1e293b] overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
            <div class="p-6 text-white">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    Developer API
                </h2>

                <div class="bg-slate-800/50 rounded-lg p-6 mb-8 border border-white/5">
                    <h3 class="text-lg font-semibold mb-2 text-slate-200">Your Project API Key</h3>
                    <p class="text-slate-400 text-sm mb-4">Use this key to authenticate your requests to the Health
                        Monitor API.</p>

                    <div
                        class="flex items-center gap-2 bg-black/30 p-3 rounded border border-slate-600 font-mono text-sm group relative">
                        <span class="text-emerald-400 flex-1 break-all">{{ $store->api_key }}</span>
                        <button onclick="navigator.clipboard.writeText('{{ $store->api_key }}')"
                            class="text-slate-400 hover:text-white transition-colors p-1" title="Copy Key">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="prose prose-invert max-w-none">
                    <h3>Integration Examples</h3>

                    <h4>1. Send Custom Telemetry (curl)</h4>
                    <pre class="bg-slate-900 rounded-lg p-4 text-sm font-mono overflow-x-auto border border-white/10">
curl -X POST {{ url('/api/v1/telemetry') }} \
  -H "Authorization: Bearer {{ $store->api_key }}" \
  -H "Content-Type: application/json" \
  -d '{
    "metric": "checkout_latency",
    "value": 120,
    "unit": "ms"
  }'</pre>

                    <h4 class="mt-6">2. Get Slow Queries</h4>
                    <pre class="bg-slate-900 rounded-lg p-4 text-sm font-mono overflow-x-auto border border-white/10">
curl -X GET {{ url('/api/v1/performance/slow-queries') }} \
  -H "Authorization: Bearer {{ $store->api_key }}"</pre>
                </div>
            </div>
        </div>
    </div>
</x-settings-layout>