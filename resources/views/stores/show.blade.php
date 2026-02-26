<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                    <span class="font-bold text-lg">{{ substr($store->name, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-white leading-tight">
                        {{ $store->name }}
                    </h2>
                    <p class="text-xs text-slate-400">{{ $store->domain ?? 'No Domain Configured' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('stores.edit', $store) }}"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition border border-white/5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Settings
                </a>
                <span
                    class="px-3 py-1.5 text-xs font-mono font-bold rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-wider">
                    {{ $store->tier }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-8">

            <!-- Stats Overview Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Status Card -->
                <div class="bg-[#1e293b] rounded-xl border border-white/10 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold tracking-wider">Current Status</p>
                        <h3 class="text-xl font-bold text-white mt-1">Operational</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                    </div>
                </div>

                <!-- API Usage -->
                <div class="bg-[#1e293b] rounded-xl border border-white/10 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold tracking-wider">API Key</p>
                        <code
                            class="text-sm font-mono text-emerald-400 mt-1 block truncate max-w-[150px]">{{ $store->api_key }}</code>
                    </div>
                    <button onclick="navigator.clipboard.writeText('{{ $store->api_key }}')"
                        class="text-slate-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>

                <!-- Quick Logic Removed (API-Only Mode) -->
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content (Integration & Simulator) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Integration Code Block -->
                    <div class="bg-[#1e293b] rounded-xl border border-white/10 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-white/5 bg-slate-900/50 flex justify-between items-center">
                            <h3 class="font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                Laravel SDK Integration
                            </h3>
                            <span class="text-xs text-slate-500">Add to .env</span>
                        </div>
                        <div class="p-6 relative group">
                            <div class="absolute top-4 right-4">
                                <button
                                    onclick="navigator.clipboard.writeText(document.getElementById('install-code').innerText)"
                                    class="px-3 py-1.5 bg-slate-800 hover:bg-emerald-600 text-white text-xs rounded-lg transition-all border border-white/10 opacity-0 group-hover:opacity-100 shadow-xl">
                                    Copy snippet
                                </button>
                            </div>
                            <pre id="install-code"
                                class="font-mono text-sm text-blue-300 bg-[#0f172a] p-4 rounded-lg overflow-x-auto border border-white/5 select-all"># Install via Composer
composer require aihealth/laravel-sdk

# Add these to your .env file
AIHEALTH_DSN={{ rtrim(config('app.url'), '/') }}/api/ingest
AIHEALTH_PROJECT_KEY={{ $store->public_key ?? $store->api_key }}
AIHEALTH_PROJECT_ID={{ $store->id }}</pre>
                        </div>
                    </div>

                    <!-- Simulator -->
                    <div class="bg-[#1e293b] rounded-xl border border-white/10 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-white/5 bg-slate-900/50 flex justify-between items-center">
                            <h3 class="font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Telemetry Simulator
                            </h3>
                            <span id="simulator-status" class="text-xs font-mono text-slate-500">Idle</span>
                        </div>
                        <div class="p-6">
                            <p class="text-slate-400 text-sm mb-6">Send test events to verify your dashboard
                                integration.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <button onclick="sendTestSignal('ok')"
                                    class="flex items-center justify-center gap-2 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5 hover:bg-emerald-500/10 text-emerald-400 font-bold transition-all">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simulate Healthy Check
                                </button>
                                <button onclick="sendTestSignal('critical')"
                                    class="flex items-center justify-center gap-2 p-4 rounded-xl border border-red-500/20 bg-red-500/5 hover:bg-red-500/10 text-red-400 font-bold transition-all">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Simulate Critical Incident
                                </button>
                            </div>

                            <!-- Console -->
                            <div class="mt-4 bg-[#0f172a] rounded-lg border border-white/5 p-3 h-32 overflow-y-auto font-mono text-xs"
                                id="simulator-log">
                                <div class="text-slate-600">> Ready for input...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Configuration) -->
                <div class="space-y-6">
                    <div class="bg-[#1e293b] rounded-xl border border-white/10 p-6">
                        <h3 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">Project Config</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-slate-500 font-bold block mb-1">Project Name</label>
                                <p class="text-slate-300 text-sm">{{ $store->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 font-bold block mb-1">Domain</label>
                                <p class="text-slate-300 text-sm">{{ $store->domain ?? 'Not configured' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 font-bold block mb-1">Created</label>
                                <p class="text-slate-300 text-sm">{{ $store->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-white/5">
                            <form method="POST" action="{{ route('stores.destroy', $store) }}"
                                onsubmit="return confirm('Are you sure? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm rounded-lg border border-red-500/20 transition-colors">
                                    Delete Project
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function sendTestSignal(status) {
                    const log = document.getElementById('simulator-log');
                    const statusLabel = document.getElementById('simulator-status');
                    const timestamp = new Date().toLocaleTimeString();

                    statusLabel.innerText = 'Sending...';
                    statusLabel.className = 'text-xs font-mono text-blue-400 animate-pulse';
                    log.innerHTML += `<div class="text-slate-400"><span class="text-slate-600">[${timestamp}]</span> Sending ${status} signal...</div>`;
                    log.scrollTop = log.scrollHeight;

                    fetch('{{ url('/api/v1/telemetry') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            api_key: '{{ $store->api_key }}',
                            checks: [
                                {
                                    name: 'Manual Browser Test',
                                    type: 'browser',
                                    status: status,
                                    latency: Math.floor(Math.random() * 100)
                                }
                            ]
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            statusLabel.innerText = 'Success';
                            statusLabel.className = 'text-xs font-mono text-emerald-400';

                            log.innerHTML += `<div class="text-emerald-400"><span class="text-slate-600">[${timestamp}]</span> Response: ${data.status}</div>`;
                            if (data.incidents_triggered > 0) {
                                log.innerHTML += `<div class="text-red-400 font-bold"><span class="text-slate-600">[${timestamp}]</span> ⚠️ INCIDENT TRIGGERED!</div>`;
                            }
                            log.scrollTop = log.scrollHeight;
                        })
                        .catch(error => {
                            statusLabel.innerText = 'Error';
                            statusLabel.className = 'text-xs font-mono text-red-500';
                            log.innerHTML += `<div class="text-red-500"><span class="text-slate-600">[${timestamp}]</span> Error: ${error}</div>`;
                            log.scrollTop = log.scrollHeight;
                        });
                }
            </script>

        </div>
    </div>
</x-app-layout>