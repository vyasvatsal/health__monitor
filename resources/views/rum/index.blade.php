@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight">
        {{ __('Performance UX & CTA Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Title Section -->
            <div>
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                    Real User UX & Speed
                </h3>
                <p class="mt-2 text-slate-400">Live native tracking of Core Web Vitals and Call-To-Action interaction rates
                    from actual visitors on your projects.</p>
            </div>

            @if($metrics->isEmpty())
                <div class="bg-[#1e293b] border border-white/5 rounded-lg p-12 text-center">
                    <svg class="w-12 h-12 text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h4 class="text-lg font-bold text-white">No RUM Data Collected Yet</h4>
                    <p class="text-sm text-slate-400 mt-2 max-w-md mx-auto">Ensure you have generated your Private Tracking Key
                        in the <strong>Connection Guide</strong> and added the <code
                            class="text-emerald-400 font-mono">@aihealth</code> directive to your client project.</p>
                    <a href="{{ route('settings.connection') }}"
                        class="mt-6 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        View Connection Guide
                    </a>
                </div>
            @else

                <!-- Top Level Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Tracked URLs -->
                    <div
                        class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 mb-1 uppercase tracking-wider">Tracked Pages</h4>
                            <span class="text-3xl font-bold text-white">{{ $metrics->count() }}</span>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>

                    <!-- Total CTA Clicks -->
                    <div
                        class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 mb-1 uppercase tracking-wider">Total CTA Interactions
                            </h4>
                            <span class="text-3xl font-bold text-white">{{ number_format($totalCtaClicks) }}</span>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777" />
                            </svg>
                        </div>
                    </div>

                    <!-- Global Avg Load Time -->
                    <div
                        class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 mb-1 uppercase tracking-wider">Global Avg Load</h4>
                            <span class="text-3xl font-bold text-white">{{ round($metrics->avg('avg_load_time')) }} <span
                                    class="text-lg text-slate-400">ms</span></span>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Matrix Table -->
                <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                    <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center bg-black/20">
                        <h3 class="text-lg font-bold text-white">URL Performance Matrix</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-black/10 text-slate-400 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-medium">URL Path</th>
                                    <th class="px-6 py-4 font-medium text-center">Grade</th>
                                    <th class="px-6 py-4 font-medium text-right">Avg Load (ms)</th>
                                    <th class="px-6 py-4 font-medium text-right">Avg JS (ms)</th>
                                    <th class="px-6 py-4 font-medium text-right">Sample Size</th>
                                    <th class="px-6 py-4 font-medium text-center">CTA Clicks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($metrics as $metric)
                                    @php
                                        $grade = $latestGrades[$metric->url_path] ?? 'N/A';
                                        $clicksForUrl = isset($ctaBreakdown[$metric->url_path]) ? array_sum(array_column($ctaBreakdown[$metric->url_path], 'clicks')) : 0;

                                        $gradeColor = match ($grade) {
                                            'A' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                            'B' => 'bg-emerald-400/20 text-emerald-300 border-emerald-400/30',
                                            'C' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                            'D' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                            'F' => 'bg-red-500/20 text-red-500 border-red-500/30',
                                            default => 'bg-slate-700/50 text-slate-400 border-slate-600',
                                        };
                                    @endphp
                                    <tr class="hover:bg-white/5 transition-colors group">
                                        <!-- URL PATH -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="text-white font-mono text-sm block truncate max-w-xs"
                                                    title="{{ $metric->url_path }}">
                                                    {{ $metric->url_path }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- GRADE -->
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center px-2.5 py-0.5 rounded text-xs font-bold border {{ $gradeColor }}">
                                                {{ $grade }}
                                            </span>
                                        </td>

                                        <!-- LOAD TIME -->
                                        <td class="px-6 py-4 text-right">
                                            <span
                                                class="text-sm font-medium {{ $metric->avg_load_time > 3000 ? 'text-orange-400' : 'text-slate-300' }}">
                                                {{ number_format($metric->avg_load_time) }}
                                            </span>
                                        </td>

                                        <!-- JS TIME -->
                                        <td class="px-6 py-4 text-right cursor-help"
                                            title="Time taken for client JS to initialize interactivity">
                                            <span
                                                class="text-sm font-medium {{ $metric->avg_js_time > 1000 ? 'text-yellow-400' : 'text-slate-400' }}">
                                                {{ number_format($metric->avg_js_time) }}
                                            </span>
                                        </td>

                                        <!-- SAMPLE SIZE -->
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm text-slate-500">{{ number_format($metric->total_visits) }}
                                                visits</span>
                                        </td>

                                        <!-- CTA SUMMARY AND MODAL TOGGLE -->
                                        <td class="px-6 py-4 text-center">
                                            @if($clicksForUrl > 0)
                                                <button
                                                    onclick="document.getElementById('modal-{{ md5($metric->url_path) }}').classList.remove('hidden')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 rounded-full text-indigo-400 text-xs font-medium transition-colors">
                                                    {{ number_format($clicksForUrl) }} Clicks
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-slate-600 text-xs">No Clicks</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CTA Modals -->
                @foreach($ctaBreakdown as $url => $clicks)
                    <div id="modal-{{ md5($url) }}" class="hidden fixed inset-0 z-[100] overflow-y-auto"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true"
                                onclick="this.parentElement.parentElement.classList.add('hidden')"></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <div
                                class="inline-block align-bottom bg-[#0f172a] rounded-xl border border-white/10 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                <div
                                    class="bg-[#1e293b] px-4 py-5 border-b border-white/5 sm:px-6 flex justify-between items-center">
                                    <h3 class="text-lg leading-6 font-medium text-white break-all" id="modal-title">
                                        CTA Breakdown: <span class="text-indigo-400 font-mono text-sm">{{ $url }}</span>
                                    </h3>
                                    <button onclick="document.getElementById('modal-{{ md5($url) }}').classList.add('hidden')"
                                        class="text-slate-400 hover:text-white">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="bg-[#0f172a] px-4 py-5 sm:p-6 p-0 overflow-y-auto max-h-[60vh]">
                                    <ul class="divide-y divide-white/5">
                                        @foreach(collect($clicks)->sortByDesc('clicks') as $click)
                                            <li class="py-4">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                                                {{ $click['tag'] }}
                                                            </span>
                                                            <span class="text-white font-medium">"{{ $click['text'] }}"</span>
                                                        </div>
                                                        <div
                                                            class="text-xs font-mono text-slate-500 break-all bg-slate-900/50 p-1 rounded">
                                                            class="{{ $click['classes'] }}"
                                                        </div>
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0 flex flex-col items-end">
                                                        <span
                                                            class="text-lg font-bold text-emerald-400">{{ number_format($click['clicks']) }}</span>
                                                        <span
                                                            class="text-[10px] text-slate-500 uppercase tracking-wider">interactions</span>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            @endif
        </div>
    </div>
@endsection