@extends('layouts.app')

@section('content')
    <div class="py-6 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col">
            <div class="flex justify-between items-center mb-5">
                <h1 class="text-xl font-bold text-white">Dashboard</h1>
                <button onclick="analyzeStoreHealth()"
                    class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-sm font-bold rounded-lg shadow-lg shadow-purple-500/20 flex items-center gap-2 transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Analyze with AI
                </button>
            </div>

            <!-- AI Analysis Modal -->
            <div id="aiModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
                <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeAiModal()"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl px-4">
                    <div class="bg-[#1e293b] rounded-2xl border border-white/10 shadow-2xl overflow-hidden relative">
                        <!-- Loading State -->
                        <div id="aiLoading" class="hidden p-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 mb-6 relative">
                                <div class="absolute inset-0 rounded-full border-4 border-indigo-500/30"></div>
                                <div class="absolute inset-0 rounded-full border-4 border-t-indigo-500 animate-spin"></div>
                                <div class="absolute inset-4 rounded-full bg-indigo-500/20 blur-md animate-pulse"></div>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Analyzing Store Health...</h3>
                            <p class="text-slate-400">Gemini AI is generating insights based on your metrics.</p>
                        </div>

                        <!-- Results State -->
                        <div id="aiResults" class="p-8 hidden">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">AI Health Analysis</h3>
                                        <p class="text-xs text-slate-400">Powered by Google Gemini</p>
                                    </div>
                                </div>
                                <button onclick="closeAiModal()" class="text-slate-400 hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="bg-indigo-500/5 rounded-xl border border-indigo-500/10 p-4 mb-6">
                                <div id="aiContent" class="prose prose-invert prose-sm max-w-none text-slate-300">
                                    <!-- AI Content Injected Here -->
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button onclick="closeAiModal()"
                                    class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white text-sm font-bold rounded-lg transition-colors">
                                    Close
                                </button>
                                <a href="{{ route('settings.index') }}"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-lg transition-colors shadow-lg shadow-indigo-500/20">
                                    View Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Health Score -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">
                                    AI Health Score
                                </div>
                                <div class="text-2xl font-bold text-white tracking-tight">
                                    {{ $healthScore->score ?? 0 }}<span
                                        class="text-sm text-slate-500 font-normal">/100</span>
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Breakdown -->
                        <div class="mt-3 grid grid-cols-2 gap-2 text-[10px]">
                            @php
                                $metrics = json_decode($healthScore->metrics_json ?? '{}', true);
                            @endphp
                            @foreach(['performance' => 'Perf', 'ux' => 'UX', 'conversion' => 'Conv', 'trust' => 'Trust'] as $key => $label)
                                <div class="flex flex-col">
                                    <span class="text-slate-500">{{ $label }}</span>
                                    <div class="w-full bg-slate-700 h-1 rounded-full mt-1">
                                        <div class="bg-emerald-500 h-1 rounded-full" style="width: {{ $metrics[$key] ?? 0 }}%">
                                        </div>
                                    </div>
                                    <span class="text-emerald-400 font-medium mt-0.5">{{ $metrics[$key] ?? 0 }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Total Requests -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">
                                    Total Requests
                                </div>
                                <div class="text-2xl font-bold text-white tracking-tight">
                                    {{ number_format($totalRequests ?? 2400000) }}
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center text-xs">
                            <span class="text-emerald-400 font-medium flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                12.5%
                            </span>
                            <span class="text-slate-500 ml-2">vs last hour</span>
                        </div>
                    </div>
                </div>

                <!-- Avg Latency -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">
                                    Avg Latency
                                </div>
                                <div class="text-2xl font-bold text-white tracking-tight">
                                    {{ $avgLatency ?? 45 }}<span class="text-sm text-slate-500 font-normal">ms</span>
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center text-xs">
                            <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-medium">
                                Fast
                            </span>
                            <span class="text-slate-500 ml-2">Global Avg</span>
                        </div>
                    </div>
                </div>

                <!-- Error Rate -->
                <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">
                                    Error Rate
                                </div>
                                <div class="text-2xl font-bold text-white tracking-tight">
                                    {{ $errorRate ?? 0.02 }}<span class="text-sm text-slate-500 font-normal">%</span>
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center text-xs">
                            <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-medium">
                                Stable
                            </span>
                            <span class="text-slate-500 ml-2">Last 24h</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insights Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Financial Impact -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-white font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Predictive Revenue Impact
                                </h3>
                                <p class="text-slate-400 text-xs mt-1">Based on Amazon's Latency Rule (100ms = 1% drop)</p>
                            </div>
                            @if($revenueLoss['is_optimal'])
                                <span
                                    class="bg-emerald-500/10 text-emerald-400 text-xs font-bold px-2 py-1 rounded">OPTIMAL</span>
                            @else
                                <span class="bg-red-500/10 text-red-400 text-xs font-bold px-2 py-1 rounded">AT RISK</span>
                            @endif
                        </div>

                        <div class="flex items-end gap-2">
                            <div class="text-3xl font-bold text-white tracking-tight">
                                ${{ number_format($revenueLoss['loss_amount']) }}
                            </div>
                            <div class="text-sm text-slate-500 mb-1">/ month potential loss</div>
                        </div>

                        <div class="mt-4 bg-slate-700/30 rounded p-3">
                            <div class="flex justify-between items-center text-xs text-slate-300">
                                <span>Current Latency Excess</span>
                                <span
                                    class="text-white font-mono">{{ ($revenueLoss['excess_ms'] ?? 0) > 0 ? '+' . ($revenueLoss['excess_ms'] ?? 0) . 'ms' : '0ms' }}</span>
                            </div>
                            <div class="w-full bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full"
                                    style="width: {{ min($revenueLoss['loss_percentage'] * 5, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-slate-500 mt-1">
                                <span>Target: 100ms</span>
                                <span class="text-amber-400">{{ $revenueLoss['loss_percentage'] }}% conversion drop</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optimization Opportunity (Placeholder for Module 2/6) -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="p-5 flex flex-col h-full justify-between">
                        <div>
                            <h3 class="text-white font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Optimization Opportunity
                            </h3>
                            <p class="text-slate-400 text-xs mt-1">Recover lost revenue by optimizing assets</p>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-slate-300 mb-3">Optimize images and scripts to save
                                <strong>{{ ($revenueLoss['excess_ms'] ?? 0) > 0 ? round(($revenueLoss['excess_ms'] ?? 0) * 0.4) : 0 }}ms</strong>
                                load time.
                            </p>
                            <button
                                class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 px-4 rounded transition-colors w-full">
                                View Autonomous Fixes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Live Traffic Chart -->
                <div class="lg:col-span-2 bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                    <div class="p-4 border-b border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-semibold text-white">Live Traffic Ingress</h3>
                            <p class="text-slate-400 text-xs mt-0.5">Last 24 hours performance</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded hover:bg-emerald-500/30 transition-colors">24H</button>
                        </div>
                    </div>
                    <div class="p-4 relative h-72 w-full">
                        <!-- Chart -->
                        <div class="h-full w-full">
                            <canvas id="trafficChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                    <div class="p-4 border-b border-white/5">
                        <h3 class="text-base font-semibold text-white flex items-center gap-2">
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)] animate-pulse">
                            </div>
                            Component Status
                        </h3>
                    </div>
                    <div class="p-0">
                        @forelse($components ?? [] as $check)
                            @if(is_object($check))
                                @php
                                    $status = optional($check->latestResult)->status ?? 'unknown';
                                    $color = match ($status) {
                                        'ok' => 'bg-emerald-500',
                                        'warning' => 'bg-amber-500',
                                        'critical' => 'bg-red-500',
                                        default => 'bg-slate-500'
                                    };
                                    $textColor = match ($status) {
                                        'ok' => 'text-emerald-400',
                                        'warning' => 'text-amber-400',
                                        'critical' => 'text-red-400',
                                        default => 'text-slate-400'
                                    };
                                @endphp
                                <div
                                    class="flex items-center justify-between p-3 border-b border-white/5 hover:bg-white/5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $color }}"></div>
                                        <span
                                            class="text-slate-300 text-sm font-medium">{{ $check->name ?? 'Unknown Component' }}</span>
                                    </div>
                                    <span
                                        class="{{ $textColor }} text-[10px] font-bold px-1.5 py-0.5 bg-white/5 rounded uppercase">{{ $status }}</span>
                                </div>
                            @endif
                        @empty
                            <div class="text-slate-500 text-center py-4 text-sm">No components monitored.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Alerts -->
            <div class="mt-4 bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                <div class="p-4 border-b border-white/5">
                    <h3 class="text-base font-semibold text-white">Recent Alerts</h3>
                </div>
                <div class="p-4 pt-1">
                    @forelse($recentAlerts ?? [] as $alert)
                        @if(is_object($alert))
                            @php
                                $bg = match ($alert->severity ?? 'info') {
                                    'critical' => 'border-red-500/50 bg-red-500/10',
                                    'warning' => 'border-amber-500/50 bg-amber-500/10',
                                    default => 'border-blue-500/50 bg-blue-500/10',
                                };
                                $tagColor = match ($alert->severity ?? 'info') {
                                    'critical' => 'text-red-400',
                                    'warning' => 'text-amber-400',
                                    default => 'text-blue-400',
                                };
                            @endphp
                            <div class="mt-3 p-3 rounded-lg border {{ $bg }}">
                                <div class="flex justify-between items-start mb-0.5">
                                    <span
                                        class="text-[10px] font-bold uppercase {{ $tagColor }}">{{ $alert->severity ?? 'INFO' }}</span>
                                    <span
                                        class="text-[10px] text-slate-500">{{ optional($alert->created_at)->diffForHumans() ?? 'Just now' }}</span>
                                </div>
                                <div class="text-xs text-slate-300">{{ $alert->title ?? 'Untitled Alert' }}</div>
                            </div>
                        @endif
                    @empty
                        <div class="text-slate-500 text-center py-3 text-sm">No recent alerts.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Specific Script (Executed by Layout AJAX) -->
    <script>
        (function () {
            const ctx = document.getElementById('trafficChart');
            if (ctx) {
                // Prepare Data
                const rawData = @json($chartData);
                const labels = rawData.map(d => d.hour + ':00');
                const counts = rawData.map(d => d.count);
                const latencies = rawData.map(d => d.latency);

                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Requests',
                                data: counts,
                                borderColor: '#10b981', // Emerald 500
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Latency (ms)',
                                data: latencies,
                                borderColor: '#3b82f6', // Blue 500
                                borderDash: [5, 5],
                                tension: 0.4,
                                pointRadius: 0,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#94a3b8' } // Slate 400
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#64748b' } // Slate 500
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#64748b' }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: { color: '#64748b' }
                            }
                        }
                    }
                });

                // Register for cleanup
                if (!window.myCharts) window.myCharts = [];
                window.myCharts.push(chart);
            }
        })();
        // Helper to get CSRF token
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        window.analyzeStoreHealth = function () {
            const modal = document.getElementById('aiModal');
            const loading = document.getElementById('aiLoading');
            const results = document.getElementById('aiResults');
            const content = document.getElementById('aiContent');

            // Show Modal & Loading
            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            results.classList.add('hidden');

            // Gather Data
            const healthData = {
                store_name: '{{ auth()->user()->name }}\'s Store',
                score: {{ $healthScore->score ?? 0 }},
                performance_score: {{ $metrics['performance'] ?? 0 }},
                ux_score: {{ $metrics['ux'] ?? 0 }},
                trust_score: {{ $metrics['trust'] ?? 0 }},
                seo_score: {{ $metrics['seo'] ?? 0 }},
                issues: [
                    @if(($revenueLoss['excess_ms'] ?? 0) > 0) 'High Latency ({{ $revenueLoss['excess_ms'] ?? 0 }}ms excess)', @endif
                    @if(($errorRate ?? 0) > 0.01) 'Elevated Error Rate', @endif
                                ]
            };

            // Call API
            fetch('{{ url('/api/v1/ai/analyze') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken() // Ensure CSRF token is sent
                },
                body: JSON.stringify({ store_data: healthData })
            })
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');
                    results.classList.remove('hidden');

                    // Format Markdown-style response
                    let formatted = data.analysis || 'No analysis available.';
                    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="text-white">$1</strong>'); // Bold
                    formatted = formatted.replace(/\n/g, '<br>'); // Newlines
                    formatted = formatted.replace(/^- (.*)/gm, '<li class="ml-4 list-disc">$1</li>'); // Bullets

                    content.innerHTML = formatted;
                })
                .catch(err => {
                    loading.classList.add('hidden');
                    results.classList.remove('hidden');
                    content.innerHTML = '<span class="text-red-400">Error: ' + err.message + '</span>';
                });
        };

        window.closeAiModal = function () {
            document.getElementById('aiModal').classList.add('hidden');
        };
    </script>
@endsection