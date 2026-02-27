<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Deep UX & CTA Analysis - {{ $store->name }}
            </h2>
            <div class="flex items-center gap-3">
                @php $primaryPage = $batchAnalyses->first(); @endphp
                @if($primaryPage)
                    <span class="text-xs text-slate-400">Scanned: {{ $primaryPage->created_at->diffForHumans() }}</span>
                    <form method="POST" action="{{ route('analysis.store', $store->id) }}">
                        @csrf
                        <input type="hidden" name="url" value="{{ $primaryPage->url }}">
                        <button type="submit" onclick="this.innerHTML='Re-scanning...'; this.classList.add('opacity-50');" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
                            Re-Scan Now
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#0f172a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($isProcessing)
                <div class="bg-[#1e293b] border border-indigo-500/30 rounded-xl p-12 text-center shadow-xl">
                    <div class="w-20 h-20 mb-6 mx-auto relative">
                        <div class="absolute inset-0 rounded-full border-4 border-indigo-500/30"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-t-indigo-500 animate-spin"></div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Analyzing Funnel Pages...</h3>
                    <p class="text-slate-400 max-w-lg mx-auto mb-4">
                        We are running deep Lighthouse scans on multiple pages across your site in the background. Once finished, our Gemini AI will aggregate the data to build a master CTA strategy.
                    </p>
                    <p class="text-sm text-indigo-400 animate-pulse">This page will automatically refresh.</p>
                    <script>setTimeout(() => window.location.reload(), 10000);</script>
                </div>
            @else
                
                @php
                    $count = $batchAnalyses->count();
                    $avgPerf = $batchAnalyses->avg('performance_score');
                    $avgAcc = $batchAnalyses->avg('accessibility_score');
                    $avgBest = $batchAnalyses->avg('best_practices_score');
                    $avgSeo = $batchAnalyses->avg('seo_score');
                    $avgTotal = ($avgPerf + $avgAcc + $avgBest + $avgSeo) / 4;

                    $grade = 'F'; $color = 'text-red-500'; $bg = 'bg-red-500/10'; $border = 'border-red-500/20';
                    if($avgTotal >= 90) { $grade = 'A'; $color = 'text-emerald-400'; $bg = 'bg-emerald-500/10'; $border = 'border-emerald-500/20'; }
                    elseif($avgTotal >= 80) { $grade = 'B'; $color = 'text-emerald-300'; $bg = 'bg-emerald-400/10'; $border = 'border-emerald-400/20'; }
                    elseif($avgTotal >= 70) { $grade = 'C'; $color = 'text-yellow-400'; $bg = 'bg-yellow-500/10'; $border = 'border-yellow-500/20'; }
                    elseif($avgTotal >= 60) { $grade = 'D'; $color = 'text-orange-400'; $bg = 'bg-orange-500/10'; $border = 'border-orange-500/20'; }
                    
                    // For Web Vitals, just using the first page (usually homepage) as a proxy for the visuals
                    $primaryPage = $batchAnalyses->first();
                @endphp

                <!-- Top Level Batch Grades -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-[#1e293b] border border-white/10 rounded-xl p-8 flex items-center justify-between shadow-lg">
                        <div>
                            <h3 class="text-slate-400 text-sm font-semibold uppercase tracking-wider mb-2">Sitewide Grade</h3>
                            <div class="text-slate-300 text-sm">Aggregated from {{ $count }} pages</div>
                        </div>
                        <div class="w-24 h-24 {{ $bg }} {{ $border }} border-2 rounded-full flex items-center justify-center">
                            <span class="text-5xl font-black {{ $color }}">{{ $grade }}</span>
                        </div>
                    </div>

                    <div class="bg-[#1e293b] border border-white/10 rounded-xl p-6 lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4 shadow-lg">
                        @foreach([
                            ['label' => 'Avg Performance', 'score' => round($avgPerf), 'color' => $avgPerf >= 90 ? 'text-emerald-400' : ($avgPerf >= 50 ? 'text-yellow-400' : 'text-red-400')],
                            ['label' => 'Avg Accessibility', 'score' => round($avgAcc), 'color' => $avgAcc >= 90 ? 'text-emerald-400' : ($avgAcc >= 50 ? 'text-yellow-400' : 'text-red-400')],
                            ['label' => 'Avg Best Practices', 'score' => round($avgBest), 'color' => $avgBest >= 90 ? 'text-emerald-400' : ($avgBest >= 50 ? 'text-yellow-400' : 'text-red-400')],
                            ['label' => 'Avg SEO', 'score' => round($avgSeo), 'color' => $avgSeo >= 90 ? 'text-emerald-400' : ($avgSeo >= 50 ? 'text-yellow-400' : 'text-red-400')]
                        ] as $metric)
                            <div class="flex flex-col items-center justify-center p-4 bg-[#0f172a] rounded-lg border border-white/5">
                                <span class="text-3xl font-bold {{ $metric['color'] }} mb-2">{{ $metric['score'] }}<span class="text-lg text-slate-500 font-normal">%</span></span>
                                <span class="text-xs text-slate-400 uppercase tracking-wide font-medium text-center">{{ $metric['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Global AI Conversion Insights -->
                    <div class="bg-[#1e293b] border border-indigo-500/30 rounded-xl p-8 shadow-xl relative overflow-hidden lg:col-span-2">
                        <div class="absolute top-0 right-0 -tr-xl w-64 h-64 bg-indigo-500/5 blur-3xl rounded-full pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 -bl-xl w-64 h-64 bg-purple-500/5 blur-3xl rounded-full pointer-events-none"></div>

                        <div class="relative z-10 flex items-center gap-3 mb-6 border-b border-indigo-500/20 pb-4">
                            <div class="w-10 h-10 rounded-lg bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white tracking-tight">Master CTA & UX Strategy</h2>
                                <div class="text-indigo-300/70 text-sm">Synthesized by Gemini 2.0 across {{ $count }} pages</div>
                            </div>
                        </div>

                        <div class="prose prose-invert prose-indigo max-w-none text-slate-300">
                            @if($masterInsights)
                                {!! \Illuminate\Support\Str::markdown($masterInsights['insight_text'] ?? 'No AI analysis generated.') !!}
                            @else
                                <p>AI is still analyzing the aggregate data. Please refresh in a moment.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Scanned Pages List -->
                    <div class="bg-[#1e293b] border border-white/10 rounded-xl p-6 shadow-lg">
                        <h3 class="text-lg font-bold text-white mb-4">Pages Scanned</h3>
                        <div class="space-y-3">
                            @foreach($batchAnalyses as $page)
                                <div class="bg-[#0f172a] rounded-lg p-3 border border-white/5">
                                    <div class="text-sm font-medium text-white truncate mb-1" title="{{ $page->url }}">
                                        {{ str_replace(parse_url($page->url, PHP_URL_SCHEME).'://'.parse_url($page->url, PHP_URL_HOST), '', $page->url) ?: '/' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="{{ $page->performance_score >= 90 ? 'text-emerald-400' : ($page->performance_score >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                            Perf: {{ $page->performance_score }}
                                        </span>
                                        <span class="text-slate-600">|</span>
                                        <span class="{{ $page->accessibility_score >= 90 ? 'text-emerald-400' : ($page->accessibility_score >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                            Acc: {{ $page->accessibility_score }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
