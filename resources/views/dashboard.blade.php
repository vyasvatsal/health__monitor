@extends('layouts.app')

@section('content')
    <div class="py-6 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col">
            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-4">
                    <!-- Project Switcher -->
                    <div class="relative group">
                        <button
                            class="flex items-center gap-2 text-xl font-bold text-white hover:text-slate-200 transition-colors">
                            {{ optional($currentStore)->name ?? 'Dashboard' }}
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Dropdown -->
                        <div
                            class="absolute left-0 mt-2 w-56 bg-[#1e293b] border border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                            <div class="py-1">
                                @foreach($allStores as $s)
                                    <a href="{{ route('dashboard', ['store_id' => $s->id]) }}"
                                        class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white flex justify-between items-center">
                                        {{ $s->name }}
                                        @if($currentStore && $s->id === $currentStore->id)
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </a>
                                @endforeach
                                <div class="border-t border-slate-700 my-1"></div>
                                <a href="{{ route('stores.create') }}"
                                    class="block px-4 py-2 text-sm text-emerald-400 hover:bg-slate-700 font-medium">
                                    + Create New Project
                                </a>
                            </div>
                        </div>
                    </div>

                    <span
                        class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-xs text-emerald-400 font-mono tracking-wide">
                        UPTIME 30D: {{ $uptime30d }}%
                    </span>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('status') }}" target="_blank"
                        class="px-4 py-2 bg-[#1e293b] hover:bg-[#334155] text-white text-sm font-medium rounded-lg border border-slate-700 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Public Status
                    </a>
                    <a href="{{ route('projects.database', $currentStore->id) }}"
                        class="px-4 py-2 bg-[#1e293b] hover:bg-[#334155] text-white text-sm font-medium rounded-lg border border-slate-700 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 1.105 2.239 2 5 2s5-.895 5-2V7M4 7c0 1.105 2.239 2 5 2s5-.895 5-2M4 7c0-1.105 2.239-2 5-2s5 .895 5 2m0 5c0 1.105-2.239 2-5 2s-5-.895-5-2" />
                        </svg>
                        DB Explorer
                    </a>
                    <a href="{{ route('incidents.create') }}"
                        class="px-4 py-2 bg-[#1e293b] hover:bg-[#334155] text-white text-sm font-medium rounded-lg border border-slate-700 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Report Incident
                    </a>
                    <button onclick="analyzeStoreHealth()"
                        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-sm font-bold rounded-lg shadow-lg shadow-purple-500/20 flex items-center gap-2 transition-all hover:scale-105">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Analyze with AI
                    </button>
                </div>
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
                                <a href="{{ route('settings.developer') }}" target="_blank"
                                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-bold rounded-lg transition-colors border border-slate-600">
                                    API Key
                                </a>
                                <a href="{{ route('settings.index') }}"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-lg transition-colors shadow-lg shadow-indigo-500/20">
                                    View Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($executiveSummary)
                <!-- Morning Update Digest -->
                <div class="mb-6 group relative">
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-10 group-hover:opacity-20 transition duration-1000 group-hover:duration-200">
                    </div>
                    <div
                        class="relative bg-[#1e293b]/80 backdrop-blur-xl rounded-2xl border border-white/10 p-6 shadow-2xl overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 poly-glow-ai opacity-10"></div>
                        <div class="flex flex-col md:flex-row gap-6 items-start relative z-10">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-14 h-14 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h2 class="text-xl font-bold text-white tracking-tight">AI Executive Summary</h2>
                                    <span
                                        class="px-2 py-0.5 bg-indigo-500/20 text-indigo-400 text-[10px] font-bold rounded-full border border-indigo-500/20 uppercase">Morning
                                        Digest</span>
                                </div>
                                <div class="prose prose-invert prose-sm max-w-none text-slate-300 leading-relaxed">
                                    {!! \Illuminate\Support\Str::markdown($executiveSummary->content) !!}
                                </div>
                                <div class="mt-4 flex items-center gap-4 text-[10px] text-slate-500 font-mono">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor font-mono">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        PERIOD: {{ $executiveSummary->period_start->format('M d, H:i') }} -
                                        {{ $executiveSummary->period_end->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                                    {{ optional($healthScore)->score ?? 100 }}<span
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
                                $metrics = optional($healthScore)->metrics_json ?? ['performance' => 100, 'ux' => 100, 'conversion' => 100, 'trust' => 100];
                                if (is_string($metrics))
                                    $metrics = json_decode($metrics, true); // Fallback for old records
                            @endphp
                            @foreach(['performance' => 'Perf', 'ux' => 'UX', 'conversion' => 'Conv', 'trust' => 'Trust'] as $key => $label)
                                <div class="flex flex-col">
                                    <span class="text-slate-500">{{ $label }}</span>
                                    <div class="w-full bg-slate-700 h-1 rounded-full mt-1">
                                        <div class="bg-emerald-500 h-1 rounded-full"
                                            style="width: {{ $metrics[$key] ?? 100 }}%">
                                        </div>
                                    </div>
                                    <span class="text-emerald-400 font-medium mt-0.5">{{ $metrics[$key] ?? 100 }}%</span>
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
                <!-- AI Status Card -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold">AI Service Status</h3>
                                    <p class="text-slate-400 text-xs">Groq LLM Engine</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(($aiHealth['status'] ?? 'error') === 'ok')
                                    <div
                                        class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                                        <span class="relative flex h-2 w-2">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        <span class="text-xs font-medium text-emerald-400">Operational</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $aiHealth['latency'] ?? 0 }}ms</span>
                                @else
                                    <div
                                        class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-500/10 border border-red-500/20">
                                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                        <span class="text-xs font-medium text-red-400">Offline</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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

                <!-- Slowest Route (New Phase 2) -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-white font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Slowest Detected Route
                                </h3>
                                <p class="text-slate-400 text-xs mt-1">Slowest request in the last hour</p>
                            </div>
                        </div>

                        @if($slowestRoute)
                            <div class="flex items-end gap-2">
                                <div class="text-3xl font-bold text-white tracking-tight">
                                    {{ $slowestRoute['duration'] }}ms
                                </div>
                                <div class="text-sm text-slate-500 mb-1">latency</div>
                            </div>
                            <div class="mt-4 bg-slate-700/30 rounded p-3">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-1.5 py-0.5 rounded bg-slate-600 text-slate-300 text-[10px] font-bold uppercase">{{ $slowestRoute['method'] }}</span>
                                        <span class="text-xs text-white truncate w-48"
                                            title="{{ $slowestRoute['url'] }}">{{ parse_url($slowestRoute['url'], PHP_URL_PATH) }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 text-right">
                                        {{ \Carbon\Carbon::parse($slowestRoute['timestamp'])->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-slate-500 text-sm py-4">No slow queries detected.</div>
                        @endif
                    </div>
                </div>

                <!-- Test Alerts (Phase 3) -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-white font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    Test Alerts
                                </h3>
                                <p class="text-slate-400 text-xs mt-1">Verify your notification channels</p>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-sm text-slate-300 mb-3">Trigger a simulated critical alert to check Slack/Email
                                integration.</p>
                            <form action="{{ route('settings.alerts.test') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-yellow-600 hover:bg-yellow-500 text-white text-xs font-bold py-2 px-4 rounded transition-colors w-full flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Simulate Critical Alert
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Score (Phase 4) -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-white font-bold flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Security Status
                                </h3>
                                <p class="text-slate-400 text-xs mt-1">Automated vulnerability scan</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-3xl font-bold text-white tracking-tight">
                                {{ $securityResult['score'] }}<span class="text-sm text-slate-500">/100</span>
                            </div>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                {{ $securityResult['status'] }}
                            </span>
                        </div>

                        @if(!empty($securityResult['issues']))
                            <div class="mt-3 space-y-1">
                                @foreach($securityResult['issues'] as $issue)
                                    <div class="text-[10px] text-red-400 flex items-center gap-1">
                                        <span>•</span> {{ $issue }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-3 text-xs text-emerald-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                No critical issues found.
                            </div>
                        @endif
                    </div>
                </div>
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
                            <form action="{{ route('optimization.run') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 px-4 rounded transition-colors w-full flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Run Autonomous Optimizations
                                </button>
                            </form>
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
                <!-- System Health Metrics -->
                <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 mb-6">
                    <div class="p-4 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            Server Health Analytics
                        </h3>
                        @if($systemHealth)
                            <span class="text-xs text-slate-500 font-mono">Updated {{ $systemHealth['last_checked'] }}</span>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($systemHealth)
                            <div
                                class="grid grid-cols-1 gap-4 divide-y divide-white/5 sm:grid-cols-3 sm:divide-y-0 sm:divide-x">
                                <!-- CPU -->
                                <div class="px-4 py-3 sm:py-0">
                                    <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider">CPU Load</dt>
                                    <dd class="mt-2 flex items-baseline gap-2">
                                        <span
                                            class="text-2xl font-bold tracking-tight text-white">{{ is_array($systemHealth['cpu_load']) ? number_format($systemHealth['cpu_load'][0], 2) : ($systemHealth['cpu_load'] ?? 'N/A') }}</span>
                                        <span class="text-sm font-medium text-slate-500">1m avg</span>
                                    </dd>
                                </div>
                                <!-- Memory -->
                                <div class="px-4 py-3 sm:py-0">
                                    <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider">Memory Usage</dt>
                                    <dd class="mt-2 flex items-baseline gap-2">
                                        <span
                                            class="text-2xl font-bold tracking-tight text-white">{{ number_format($systemHealth['memory_usage_mb'] ?? 0, 1) }}</span>
                                        <span class="text-sm font-medium text-slate-500">MB</span>
                                    </dd>
                                </div>
                                <!-- Database -->
                                <div class="px-4 py-3 sm:py-0">
                                    <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider">Database</dt>
                                    <dd class="mt-2 flex items-center gap-2">
                                        @if($systemHealth['db_connected'])
                                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <span class="text-lg font-bold tracking-tight text-emerald-400">Connected</span>
                                        @else
                                            <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                            <span class="text-lg font-bold tracking-tight text-red-400">Disconnected</span>
                                        @endif
                                    </dd>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-slate-500 mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <p class="text-sm text-slate-400 mb-2">No system health data found for this project.</p>
                                <p class="text-xs text-slate-500">Install the Laravel SDK and run <code
                                        class="bg-slate-800 text-emerald-400 px-1 py-0.5 rounded">php artisan aihealth:health</code>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

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

            <!-- Priority Alerts Hub -->
            <div class="mt-4 bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                <div class="p-4 border-b border-white/5 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Priority Alerts Hub
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Severity Order</span>
                    </div>
                </div>
                <div class="p-4 pt-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($priorityAlerts ?? [] as $alert)
                        @php
                            $severityInfo = match ($alert->severity) {
                                'critical' => ['color' => 'red', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                                'warning' => ['color' => 'amber', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                                default => ['color' => 'blue', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            };
                            $isRead = !is_null($alert->read_at);
                        @endphp
                        <div
                            class="group relative p-3 rounded-lg border transition-all duration-300 {{ $isRead ? 'bg-slate-800/50 border-white/5 opacity-60' : 'bg-' . $severityInfo['color'] . '-500/10 border-' . $severityInfo['color'] . '-500/20 hover:border-' . $severityInfo['color'] . '-500/40' }} flex flex-col gap-2">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded bg-{{ $severityInfo['color'] }}-500/20 flex items-center justify-center text-{{ $severityInfo['color'] }}-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $severityInfo['icon'] }}" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-{{ $severityInfo['color'] }}-400">{{ $alert->severity }}</span>
                                </div>
                                <span
                                    class="text-[9px] text-slate-500 font-medium">{{ $alert->created_at->diffForHumans() }}</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white truncate">{{ $alert->title }}</h4>
                                <p class="text-[10px] text-slate-400 line-clamp-2 mt-1">{{ $alert->message }}</p>
                            </div>
                            @if(!$isRead)
                                <button onclick="markAlertAsRead({{ $alert->id }})"
                                    class="mt-auto pt-2 text-[9px] text-{{ $severityInfo['color'] }}-400/70 hover:text-{{ $severityInfo['color'] }}-400 font-bold uppercase tracking-tighter transition-colors text-right">
                                    Mark as Dismissed
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-700 mb-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-slate-500 text-sm">No critical alerts currently active.</p>
                        </div>
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

        window.markAlertAsRead = function (id) {
            fetch(`/alerts/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh current view or hide the element
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        };

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
                store_name: '{{ addslashes($currentStore->name ?? 'Project') }}',
                url: '{{ addslashes($currentStore->domain ?? 'Unknown Domain') }}',
                score: {{ optional($healthScore)->score ?? 0 }},
                performance_score: {{ $metrics['performance'] ?? 0 }},
                ux_score: {{ $metrics['ux'] ?? 0 }},
                trust_score: {{ $metrics['trust'] ?? 0 }},
                seo_score: {{ $metrics['seo'] ?? 0 }},
                server_health: {
                    cpu_load: {{ is_array(optional($systemHealth)['cpu_load']) ? optional($systemHealth)['cpu_load'][0] : (optional($systemHealth)['cpu_load'] ?? 'null') }},
                    memory_usage_mb: {{ optional($systemHealth)['memory_usage_mb'] ?? 'null' }},
                    db_connected: {{ optional($systemHealth)['db_connected'] ? 'true' : 'false' }}
                                            },
                issues: [
                    @if(($revenueLoss['excess_ms'] ?? 0) > 0) 'High Latency ({{ $revenueLoss['excess_ms'] ?? 0 }}ms excess)', @endif
                    @if(($errorRate ?? 0) > 0.01) 'Elevated Error Rate', @endif
                    @if(isset($systemHealth) && !$systemHealth['db_connected']) 'Database Connection Failed', @endif
                    @if(isset($systemHealth) && is_array($systemHealth['cpu_load']) && $systemHealth['cpu_load'][0] > 70) 'High CPU Utilization', @endif
                                            ],
                recent_alerts: @json($recentAlerts->pluck('title')->take(5))
            };

            // Call API
            fetch('{{ route('ai.analyze') }}', {
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