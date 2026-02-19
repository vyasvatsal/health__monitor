<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Competitor Benchmarks') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-6">

            <!-- Add Competitor Form -->
            <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                            <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Add Competitor</h3>
                    </div>

                    <form method="POST" action="{{ route('benchmarks.store') }}"
                        class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Competitor Name')"
                                class="text-slate-300 font-medium mb-2" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full bg-slate-900/50 border-slate-700/50 text-white focus:ring-purple-500 focus:border-purple-500 rounded-lg h-10 px-4"
                                placeholder="e.g. Amazon" required />
                        </div>
                        <div>
                            <x-input-label for="url" :value="__('Competitor URL')"
                                class="text-slate-300 font-medium mb-2" />
                            <x-text-input id="url" name="url" type="url"
                                class="mt-1 block w-full bg-slate-900/50 border-slate-700/50 text-white focus:ring-purple-500 focus:border-purple-500 rounded-lg h-10 px-4"
                                placeholder="https://competitor.com" required />
                        </div>
                        <div class="md:col-span-2">
                            <x-primary-button
                                class="w-full justify-center bg-purple-600 hover:bg-purple-700 h-10 rounded-lg font-semibold">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Add Competitor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Competitors Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse($competitors as $competitor)
                    @php
                        $lastResult = $competitor->results->first();
                        $winner = $lastResult->winner ?? null;
                    @endphp

                    <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 flex flex-col">
                        <!-- Header -->
                        <div class="p-6 border-b border-white/5 flex justify-between items-start bg-slate-900/20">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-white mb-1">{{ $competitor->name }}</h3>
                                <a href="{{ $competitor->url }}" target="_blank"
                                    class="text-sm text-purple-400 hover:text-purple-300 transition-colors inline-flex items-center gap-1">
                                    {{ $competitor->url }}
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <form method="POST" action="{{ route('benchmarks.scan', $competitor) }}">
                                    @csrf
                                    <button type="submit"
                                        class="p-2 bg-slate-800 hover:bg-purple-600 rounded-lg text-slate-400 hover:text-white transition-all border border-white/5"
                                        title="Run Scan">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('benchmarks.destroy', $competitor) }}"
                                    onsubmit="return confirm('Remove this competitor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-slate-800 hover:bg-red-600 rounded-lg text-slate-400 hover:text-white transition-all border border-white/5"
                                        title="Remove Competitor">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Stats & Comparison -->
                        @if($lastResult)
                            <div class="p-6 relative flex-1">
                                <!-- VS Badge -->
                                <div
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-[#0f172a] border-4 border-[#1e293b] flex items-center justify-center font-bold text-[10px] text-slate-400 z-10">
                                    VS
                                </div>

                                <div class="grid grid-cols-2 gap-5 h-full">
                                    <!-- My Store -->
                                    <div
                                        class="relative p-6 rounded-lg {{ $winner === 'me' ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-slate-900/50 border border-white/5' }}">
                                        <div class="flex justify-between items-start mb-5 h-6">
                                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">My Store
                                            </div>
                                            @if($winner === 'me')
                                                <div class="text-emerald-400 text-xs font-bold">
                                                    WINNER
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-5">
                                            <!-- TTFB -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">Speed</span>
                                                    <span
                                                        class="font-bold {{ $lastResult->my_ttfb_ms < $lastResult->competitor_ttfb_ms ? 'text-emerald-400' : 'text-white' }}">
                                                        {{ $lastResult->my_ttfb_ms >= 9000 ? 'Timeout' : $lastResult->my_ttfb_ms . 'ms' }}
                                                    </span>
                                                </div>
                                                <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                                                    @php $maxTtfb = max($lastResult->my_ttfb_ms, $lastResult->competitor_ttfb_ms, 1); @endphp
                                                    <div class="h-full rounded-full {{ $lastResult->my_ttfb_ms < $lastResult->competitor_ttfb_ms ? 'bg-emerald-500' : 'bg-slate-500' }}"
                                                        style="width: {{ min(($lastResult->my_ttfb_ms / $maxTtfb) * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Size -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">Size</span>
                                                    <span
                                                        class="font-bold {{ $lastResult->my_size_kb < $lastResult->competitor_size_kb ? 'text-emerald-400' : 'text-white' }}">
                                                        {{ $lastResult->my_size_kb }}kb
                                                    </span>
                                                </div>
                                                <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                                                    @php $maxSize = max($lastResult->my_size_kb, $lastResult->competitor_size_kb, 1); @endphp
                                                    <div class="h-full rounded-full {{ $lastResult->my_size_kb < $lastResult->competitor_size_kb ? 'bg-emerald-500' : 'bg-slate-500' }}"
                                                        style="width: {{ min(($lastResult->my_size_kb / $maxSize) * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SEO -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">SEO Check</span>
                                                    @php 
                                                        $mySeo = $lastResult->details['seo'] ?? []; 
                                                        $mySeoScore = 0;
                                                        if(!empty($mySeo['title'])) $mySeoScore += 33;
                                                        if(!empty($mySeo['description'])) $mySeoScore += 33;
                                                        if(!empty($mySeo['h1'])) $mySeoScore += 34;
                                                    @endphp
                                                    <span class="{{ $mySeoScore > 80 ? 'text-emerald-400' : ($mySeoScore > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                        {{ $mySeoScore }}%
                                                    </span>
                                                </div>
                                                <div class="flex gap-1">
                                                    <span title="Title Tag" class="px-1.5 py-0.5 rounded text-[10px] {{ !empty($mySeo['title']) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">Title</span>
                                                    <span title="Meta Description" class="px-1.5 py-0.5 rounded text-[10px] {{ !empty($mySeo['description']) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">Desc</span>
                                                    <span title="H1 Tag" class="px-1.5 py-0.5 rounded text-[10px] {{ !empty($mySeo['h1']) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">H1</span>
                                                </div>
                                            </div>

                                            <!-- Accessibility -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">Accessibility (Img Alt)</span>
                                                    @php $myAlly = $lastResult->details['ally']['score'] ?? 0; @endphp
                                                    <span class="{{ $myAlly > 80 ? 'text-emerald-400' : ($myAlly > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                        {{ $myAlly }}%
                                                    </span>
                                                </div>
                                                <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $myAlly > 80 ? 'bg-emerald-500' : ($myAlly > 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $myAlly }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Competitor -->
                                    <div
                                        class="relative p-6 rounded-lg {{ $winner === 'them' ? 'bg-red-500/10 border border-red-500/20' : 'bg-slate-900/50 border border-white/5' }}">
                                        <div class="flex justify-between items-start mb-5 h-6">
                                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                                Competitor</div>
                                            @if($winner === 'them')
                                                <div class="text-red-400 text-xs font-bold">
                                                    WINNER
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-5">
                                            <!-- TTFB -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">Speed</span>
                                                    <span
                                                        class="font-bold {{ $lastResult->competitor_ttfb_ms < $lastResult->my_ttfb_ms ? 'text-emerald-400' : 'text-white' }}">
                                                        {{ $lastResult->competitor_ttfb_ms >= 9000 ? 'Timeout' : $lastResult->competitor_ttfb_ms . 'ms' }}
                                                    </span>
                                                </div>
                                                <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $lastResult->competitor_ttfb_ms < $lastResult->my_ttfb_ms ? 'bg-emerald-500' : 'bg-slate-500' }}"
                                                        style="width: {{ min(($lastResult->competitor_ttfb_ms / $maxTtfb) * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Size -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">Size</span>
                                                    <span
                                                        class="font-bold {{ $lastResult->competitor_size_kb < $lastResult->my_size_kb ? 'text-emerald-400' : 'text-white' }}">
                                                        {{ $lastResult->competitor_size_kb }}kb
                                                    </span>
                                                </div>
                                                <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $lastResult->competitor_size_kb < $lastResult->my_size_kb ? 'bg-emerald-500' : 'bg-slate-500' }}"
                                                        style="width: {{ min(($lastResult->competitor_size_kb / $maxSize) * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SEO -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">SEO Check</span>
                                                    @php 
                                                        // Note: In my updated Runner, I am not separating my_assets/comp_assets in 'details' root anymore. 
                                                        // I returned ['seo' => ..., 'ally' => ...] in the root of the array.
                                                        // Wait, in BenchmarkRunner.php:
                                                        // return [ 'ttfb' => ..., 'assets' => [ ... 'seo' => ..., 'ally' => ... ] ]
                                                        // And then:
                                                        // 'details' => json_encode([ 'my_assets' => $myStats['assets'], 'comp_assets' => $compStats['assets'] ])
                                                        // SO YES, I DO need to access via my_assets/comp_assets.
                                                        
                                                        $compSeo = $lastResult->details['comp_assets']['seo'] ?? []; 
                                                        $compSeoScore = 0;
                                                        if(!empty($compSeo['title'])) $compSeoScore += 33;
                                                        if(!empty($compSeo['description'])) $compSeoScore += 33;
                                                        if(!empty($compSeo['h1'])) $compSeoScore += 34;
                                                    @endphp
                                                    <span class="{{ $compSeoScore > 80 ? 'text-emerald-400' : ($compSeoScore > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                        {{ $compSeoScore }}%
                                                    </span>
                                                </div>
                                                <div class="flex gap-1">
                                                    <span title="Title Tag" class="px-1.5 py-0.5 rounded text-[10px] {{ !empty($compSeo['title']) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">Title</span>
                                                    <span title="Meta Description" class="px-1.5 py-0.5 rounded text-[10px] {{ !empty($compSeo['description']) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">Desc</span>
                                                    <span title="H1 Tag" class="px-1.5 py-0.5 rounded text-[10px] {{ !empty($compSeo['h1']) ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">H1</span>
                                                </div>
                                            </div>

                                            <!-- Accessibility -->
                                            <div>
                                                <div class="flex justify-between text-xs text-slate-400 mb-2">
                                                    <span class="font-medium">Accessibility (Img Alt)</span>
                                                    @php $compAlly = $lastResult->details['comp_assets']['ally']['score'] ?? 0; @endphp
                                                    <span class="{{ $compAlly > 80 ? 'text-emerald-400' : ($compAlly > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                        {{ $compAlly }}%
                                                    </span>
                                                </div>
                                                <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $compAlly > 80 ? 'bg-emerald-500' : ($compAlly > 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $compAlly }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="px-6 py-4 bg-slate-900/30 border-t border-white/5 flex justify-between items-center text-sm">
                                <span class="text-slate-400">
                                    <svg class="w-4 h-4 inline mr-1 opacity-50" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $lastResult->created_at->diffForHumans() }}
                                </span>
                                @if($winner === 'me')
                                    <span class="text-emerald-400 flex items-center gap-2 font-semibold">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        You're Faster!
                                    </span>
                                @elseif($winner === 'them')
                                    <span class="text-red-400 flex items-center gap-2 font-semibold">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                        They're Faster
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="p-10 text-center">
                                <div
                                    class="w-16 h-16 rounded-full bg-purple-500/10 flex items-center justify-center mx-auto mb-4 border border-purple-500/20">
                                    <svg class="w-8 h-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <p class="text-slate-400 mb-5 text-sm">No benchmarks run yet</p>
                                <form method="POST" action="{{ route('benchmarks.scan', $competitor) }}">
                                    @csrf
                                    <x-secondary-button type="submit"
                                        class="!bg-purple-600/20 !text-purple-300 !border-purple-500/30 hover:!bg-purple-600/30 !rounded-lg !h-10 !px-6 !font-semibold">
                                        Run First Scan
                                    </x-secondary-button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-16 bg-[#1e293b] rounded-lg border border-dashed border-white/10">
                        <div
                            class="w-20 h-20 rounded-lg bg-purple-500/10 flex items-center justify-center mx-auto mb-5 border border-purple-500/20">
                            <svg class="w-10 h-10 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">No Competitors Yet</h3>
                        <p class="text-slate-400">Add your first competitor to start benchmarking</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>