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
                <span class="text-xs text-slate-400">Scanned: {{ $analysis->created_at->diffForHumans() }}</span>
                <form method="POST" action="{{ route('analysis.store', $store->id) }}">
                    @csrf
                    <input type="hidden" name="url" value="{{ $analysis->url }}">
                    <button type="submit" onclick="this.innerHTML='Re-scanning...'; this.classList.add('opacity-50');" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
                        Re-Scan Now
                    </button>
                </form>
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

            <!-- Top Level Grades -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Overall Grade (Average of the 4) -->
                @php
                    $avg = ($analysis->performance_score + $analysis->accessibility_score + $analysis->best_practices_score + $analysis->seo_score) / 4;
                    $grade = 'F';
                    $color = 'text-red-500';
                    $bg = 'bg-red-500/10';
                    $border = 'border-red-500/20';
                    if($avg >= 90) { $grade = 'A'; $color = 'text-emerald-400'; $bg = 'bg-emerald-500/10'; $border = 'border-emerald-500/20'; }
                    elseif($avg >= 80) { $grade = 'B'; $color = 'text-emerald-300'; $bg = 'bg-emerald-400/10'; $border = 'border-emerald-400/20'; }
                    elseif($avg >= 70) { $grade = 'C'; $color = 'text-yellow-400'; $bg = 'bg-yellow-500/10'; $border = 'border-yellow-500/20'; }
                    elseif($avg >= 60) { $grade = 'D'; $color = 'text-orange-400'; $bg = 'bg-orange-500/10'; $border = 'border-orange-500/20'; }
                @endphp
                
                <div class="bg-[#1e293b] border border-white/10 rounded-xl p-8 flex items-center justify-between shadow-lg">
                    <div>
                        <h3 class="text-slate-400 text-sm font-semibold uppercase tracking-wider mb-2">GTmetrix Grade</h3>
                        <div class="text-slate-300 text-sm">{{ parse_url($analysis->url, PHP_URL_HOST) }}</div>
                    </div>
                    <div class="w-24 h-24 {{ $bg }} {{ $border }} border-2 rounded-full flex items-center justify-center">
                        <span class="text-5xl font-black {{ $color }}">{{ $grade }}</span>
                    </div>
                </div>

                <!-- Lighthouse Scores -->
                <div class="bg-[#1e293b] border border-white/10 rounded-xl p-6 lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4 shadow-lg">
                    @foreach([
                        ['label' => 'Performance', 'score' => $analysis->performance_score, 'color' => $analysis->performance_score >= 90 ? 'text-emerald-400' : ($analysis->performance_score >= 50 ? 'text-yellow-400' : 'text-red-400')],
                        ['label' => 'Accessibility', 'score' => $analysis->accessibility_score, 'color' => $analysis->accessibility_score >= 90 ? 'text-emerald-400' : ($analysis->accessibility_score >= 50 ? 'text-yellow-400' : 'text-red-400')],
                        ['label' => 'Best Practices', 'score' => $analysis->best_practices_score, 'color' => $analysis->best_practices_score >= 90 ? 'text-emerald-400' : ($analysis->best_practices_score >= 50 ? 'text-yellow-400' : 'text-red-400')],
                        ['label' => 'SEO', 'score' => $analysis->seo_score, 'color' => $analysis->seo_score >= 90 ? 'text-emerald-400' : ($analysis->seo_score >= 50 ? 'text-yellow-400' : 'text-red-400')]
                    ] as $metric)
                        <div class="flex flex-col items-center justify-center p-4 bg-[#0f172a] rounded-lg border border-white/5">
                            <span class="text-3xl font-bold {{ $metric['color'] }} mb-2">{{ $metric['score'] }}<span class="text-lg text-slate-500 font-normal">%</span></span>
                            <span class="text-xs text-slate-400 uppercase tracking-wide font-medium text-center">{{ $metric['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Web Vitals & Visuals -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Core Web Vitals -->
                <div class="bg-[#1e293b] border border-white/10 rounded-xl p-6 shadow-lg lg:col-span-2">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Core Web Vitals Assessment
                    </h3>
                    
                    <div class="space-y-6">
                        @php
                            $vitals = $analysis->core_web_vitals ?? [];
                            $metrics = [
                                ['key' => 'lcp', 'label' => 'Largest Contentful Paint', 'desc' => 'How long until the main content loads.'],
                                ['key' => 'tbt', 'label' => 'Total Blocking Time', 'desc' => 'How much time the page was blocked from responding to user input.'],
                                ['key' => 'cls', 'label' => 'Cumulative Layout Shift', 'desc' => 'How much the page layout shifts around.'],
                            ];
                        @endphp
                        
                        @foreach($metrics as $m)
                            <div class="border-b border-white/5 pb-4 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start mb-1">
                                    <div>
                                        <div class="text-white font-medium">{{ $m['label'] }}</div>
                                        <div class="text-xs text-slate-500">{{ $m['desc'] }}</div>
                                    </div>
                                    <div class="text-lg font-bold tracking-tight
                                        {{ ($vitals[$m['key']]['score'] ?? 0) >= 90 ? 'text-emerald-400' : (($vitals[$m['key']]['score'] ?? 0) >= 50 ? 'text-yellow-400' : 'text-red-400') }}">
                                        {{ $vitals[$m['key']]['displayValue'] ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2">
                                    <div class="h-1.5 rounded-full {{ ($vitals[$m['key']]['score'] ?? 0) >= 90 ? 'bg-emerald-400' : (($vitals[$m['key']]['score'] ?? 0) >= 50 ? 'bg-yellow-400' : 'bg-red-400') }}" style="width: {{ max(5, $vitals[$m['key']]['score'] ?? 0) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Page Screenshot (Rendered by Google) -->
                @if($analysis->desktop_screenshot)
                    <div class="bg-[#1e293b] border border-white/10 rounded-xl p-4 shadow-lg overflow-hidden flex flex-col items-center justify-center">
                        <div class="w-full h-8 flex items-center gap-1 bg-slate-800 rounded-t-lg px-3 border border-white/5 border-b-0">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500/80"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/80"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></div>
                        </div>
                        <img src="{{ $analysis->desktop_screenshot }}" alt="Rendered page" class="w-full border border-white/5 rounded-b-lg shadow-xl" style="max-height: 400px; object-fit: cover; object-position: top;">
                    </div>
                @endif
            </div>

            <!-- Gemini AI Conversion Insights -->
            <div class="bg-[#1e293b] border border-indigo-500/30 rounded-xl p-8 shadow-xl relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute top-0 right-0 -tr-xl w-64 h-64 bg-indigo-500/5 blur-3xl rounded-full pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -bl-xl w-64 h-64 bg-purple-500/5 blur-3xl rounded-full pointer-events-none"></div>

                <div class="relative z-10 flex items-center gap-3 mb-6 border-b border-indigo-500/20 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight">AI Conversion & UX Insights</h2>
                        <div class="text-indigo-300/70 text-sm">Powered by Gemini 2.0</div>
                    </div>
                </div>

                <div class="prose prose-invert prose-indigo max-w-none text-slate-300">
                    {!! \Illuminate\Support\Str::markdown($analysis->ai_insights['insight_text'] ?? 'No AI analysis available for this run.') !!}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
