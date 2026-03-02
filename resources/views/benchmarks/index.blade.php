<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 leading-tight">
                {{ __('Competitor Benchmarks') }}
            </h2>
            <div class="flex items-center gap-2 text-sm text-purple-400 bg-purple-500/10 px-4 py-1.5 rounded-full border border-purple-500/20 shadow-[0_0_15px_rgba(168,85,247,0.15)]">
                <svg class="w-4 h-4 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Real-time Analytics
            </div>
        </div>
    </x-slot>

    <div class="py-8 flex-1 flex flex-col">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-8">

            <!-- Add Competitor Form -->
            <div class="bg-slate-900/40 backdrop-blur-xl overflow-hidden rounded-2xl border border-white/5 shadow-2xl relative group transition-all duration-300 hover:border-purple-500/30 hover:bg-slate-900/60">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-purple-500/30 to-transparent opacity-50 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-fuchsia-500/20 flex items-center justify-center border border-purple-500/30 shadow-[0_0_15px_rgba(168,85,247,0.15)] group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Add New Challenger</h3>
                            <p class="text-sm text-slate-400 mt-1">Benchmark your store against a competitor to find gaps</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('benchmarks.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                        @csrf
                        <div class="md:col-span-4">
                            <x-input-label for="store_id" :value="__('Select Project')" class="text-slate-300 font-medium mb-2" />
                            <select id="store_id" name="store_id" required
                                class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-4 py-3 text-white transition-all outline-none appearance-none">
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}" class="bg-slate-900">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <x-input-label for="name" :value="__('Competitor Name')" class="text-slate-300 font-medium mb-2" />
                            <input id="name" name="name" type="text"
                                class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-4 py-3 text-white placeholder-slate-600 transition-all outline-none"
                                placeholder="e.g. Amazon" required />
                        </div>
                        <div class="md:col-span-3">
                            <x-input-label for="url" :value="__('Competitor URL')" class="text-slate-300 font-medium mb-2" />
                            <input id="url" name="url" type="url"
                                class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-4 py-3 text-white placeholder-slate-600 transition-all outline-none"
                                placeholder="https://competitor.com" required />
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit"
                                class="w-full flex items-center justify-center bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 text-white font-bold h-[50px] rounded-xl shadow-[0_0_20px_rgba(168,85,247,0.3)] hover:shadow-[0_0_25px_rgba(168,85,247,0.5)] transition-all hover:scale-[1.02]">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Competitors Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                @forelse($competitors as $competitor)
                    @php
                        $lastResult = $competitor->results->first();
                        $winner = $lastResult->winner ?? null;
                    @endphp

                    <div class="bg-slate-900/40 backdrop-blur-md rounded-2xl border border-white/5 shadow-xl flex flex-col transition-all duration-300 hover:border-purple-500/20 hover:shadow-[0_0_30px_rgba(168,85,247,0.05)] hover:-translate-y-1 relative overflow-hidden group">
                        
                        <!-- Header -->
                        <div class="p-6 border-b border-white/5 flex justify-between items-start bg-gradient-to-r from-slate-900/80 to-slate-800/40 relative z-10">
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white mb-1 group-hover:text-purple-300 transition-colors">{{ $competitor->name }}</h3>
                                <a href="{{ $competitor->url }}" target="_blank"
                                    class="text-sm text-slate-400 hover:text-purple-400 transition-colors inline-flex items-center gap-1 group/link">
                                    {{ $competitor->url }}
                                    <svg class="w-3 h-3 group-hover/link:translate-x-0.5 group-hover/link:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <form method="POST" action="{{ route('benchmarks.scan', $competitor) }}">
                                    @csrf
                                    <button type="submit"
                                        class="p-2.5 bg-slate-800/80 hover:bg-purple-600 rounded-xl text-slate-400 hover:text-white transition-all border border-white/5 hover:border-purple-500 hover:shadow-[0_0_15px_rgba(168,85,247,0.4)]"
                                        title="Run Scan">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('benchmarks.destroy', $competitor) }}"
                                    onsubmit="return confirm('Remove this competitor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2.5 bg-slate-800/80 hover:bg-red-600 rounded-xl text-slate-400 hover:text-white transition-all border border-white/5 hover:border-red-500 hover:shadow-[0_0_15px_rgba(239,68,68,0.4)]"
                                        title="Remove Competitor">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Stats & Comparison -->
                        @if($lastResult)
                            <div class="p-6 relative flex-1 z-10">
                                <!-- VS Badge -->
                                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900 border-4 border-slate-800 flex items-center justify-center font-black text-xs text-transparent bg-clip-text bg-gradient-to-br from-purple-400 to-fuchsia-400 z-20 shadow-[0_0_20px_rgba(168,85,247,0.2)]">
                                    VS
                                </div>

                                <div class="grid grid-cols-2 gap-6 h-full">
                                    <!-- My Store -->
                                    <div class="relative p-6 rounded-2xl transition-colors duration-300 {{ $winner === 'me' ? 'bg-gradient-to-b from-emerald-500/10 to-emerald-500/5 border border-emerald-500/30 shadow-[inset_0_0_20px_rgba(16,185,129,0.05)]' : 'bg-slate-900/50 border border-white/5' }}">
                                        @if($winner === 'me')
                                            <div class="absolute inset-0 bg-emerald-500/5 rounded-2xl animate-pulse"></div>
                                        @endif
                                        <div class="relative z-10">
                                            <div class="flex justify-between items-start mb-6 h-6">
                                                <div class="text-sm font-black text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                                    My Store
                                                    <span class="text-[10px] text-slate-500 normal-case bg-slate-800/50 px-2 py-0.5 rounded border border-slate-700/50 truncate max-w-[100px]" title="{{ $competitor->store->name }}">
                                                        {{ $competitor->store->name }}
                                                    </span>
                                                </div>
                                                @if($winner === 'me')
                                                    <div class="px-2 py-1 rounded-md bg-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-wider border border-emerald-500/30">
                                                        WINNER
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="space-y-6">
                                                <!-- TTFB -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">Speed</span>
                                                        <span class="font-bold {{ $lastResult->my_ttfb_ms < $lastResult->competitor_ttfb_ms ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : 'text-slate-300' }}">
                                                            {{ $lastResult->my_ttfb_ms >= 9000 ? 'Timeout' : $lastResult->my_ttfb_ms . 'ms' }}
                                                        </span>
                                                    </div>
                                                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                                        @php $maxTtfb = max($lastResult->my_ttfb_ms, $lastResult->competitor_ttfb_ms, 1); @endphp
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $lastResult->my_ttfb_ms < $lastResult->competitor_ttfb_ms ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-slate-600 to-slate-500' }}"
                                                            style="width: {{ min(($lastResult->my_ttfb_ms / $maxTtfb) * 100, 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Size -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">Size</span>
                                                        <span class="font-bold {{ $lastResult->my_size_kb < $lastResult->competitor_size_kb ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : 'text-slate-300' }}">
                                                            {{ $lastResult->my_size_kb }}kb
                                                        </span>
                                                    </div>
                                                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                                        @php $maxSize = max($lastResult->my_size_kb, $lastResult->competitor_size_kb, 1); @endphp
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $lastResult->my_size_kb < $lastResult->competitor_size_kb ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-slate-600 to-slate-500' }}"
                                                            style="width: {{ min(($lastResult->my_size_kb / $maxSize) * 100, 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SEO -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">SEO Check</span>
                                                        @php 
                                                            $mySeo = $lastResult->details['seo'] ?? []; 
                                                            $mySeoScore = 0;
                                                            if(!empty($mySeo['title'])) $mySeoScore += 33;
                                                            if(!empty($mySeo['description'])) $mySeoScore += 33;
                                                            if(!empty($mySeo['h1'])) $mySeoScore += 34;
                                                        @endphp
                                                        <span class="{{ $mySeoScore > 80 ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : ($mySeoScore > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                            {{ $mySeoScore }}%
                                                        </span>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <span title="Title Tag" class="flex-1 text-center py-1 rounded border {{ !empty($mySeo['title']) ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} text-[10px] font-semibold transition-colors">Title</span>
                                                        <span title="Meta Description" class="flex-1 text-center py-1 rounded border {{ !empty($mySeo['description']) ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} text-[10px] font-semibold transition-colors">Desc</span>
                                                        <span title="H1 Tag" class="flex-1 text-center py-1 rounded border {{ !empty($mySeo['h1']) ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} text-[10px] font-semibold transition-colors">H1</span>
                                                    </div>
                                                </div>

                                                <!-- Accessibility -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">Accessibility (Img Alt)</span>
                                                        @php $myAlly = $lastResult->details['ally']['score'] ?? 0; @endphp
                                                        <span class="{{ $myAlly > 80 ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : ($myAlly > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                            {{ $myAlly }}%
                                                        </span>
                                                    </div>
                                                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $myAlly > 80 ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : ($myAlly > 50 ? 'bg-gradient-to-r from-yellow-500 to-yellow-400' : 'bg-gradient-to-r from-red-500 to-red-400') }}"
                                                            style="width: {{ $myAlly }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Competitor -->
                                    <div class="relative p-6 rounded-2xl transition-colors duration-300 {{ $winner === 'them' ? 'bg-gradient-to-b from-red-500/10 to-red-500/5 border border-red-500/30 shadow-[inset_0_0_20px_rgba(239,68,68,0.05)]' : 'bg-slate-900/50 border border-white/5' }}">
                                        @if($winner === 'them')
                                            <div class="absolute inset-0 bg-red-500/5 rounded-2xl animate-pulse"></div>
                                        @endif
                                        <div class="relative z-10">
                                            <div class="flex justify-between items-start mb-6 h-6">
                                                <div class="text-sm font-black text-slate-300 uppercase tracking-widest">Competitor</div>
                                                @if($winner === 'them')
                                                    <div class="px-2 py-1 rounded-md bg-red-500/20 text-red-400 text-[10px] font-bold uppercase tracking-wider border border-red-500/30">
                                                        WINNER
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="space-y-6">
                                                <!-- TTFB -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">Speed</span>
                                                        <span class="font-bold {{ $lastResult->competitor_ttfb_ms < $lastResult->my_ttfb_ms ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : 'text-slate-300' }}">
                                                            {{ $lastResult->competitor_ttfb_ms >= 9000 ? 'Timeout' : $lastResult->competitor_ttfb_ms . 'ms' }}
                                                        </span>
                                                    </div>
                                                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $lastResult->competitor_ttfb_ms < $lastResult->my_ttfb_ms ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-slate-600 to-slate-500' }}"
                                                            style="width: {{ min(($lastResult->competitor_ttfb_ms / $maxTtfb) * 100, 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Size -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">Size</span>
                                                        <span class="font-bold {{ $lastResult->competitor_size_kb < $lastResult->my_size_kb ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : 'text-slate-300' }}">
                                                            {{ $lastResult->competitor_size_kb }}kb
                                                        </span>
                                                    </div>
                                                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $lastResult->competitor_size_kb < $lastResult->my_size_kb ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-slate-600 to-slate-500' }}"
                                                            style="width: {{ min(($lastResult->competitor_size_kb / $maxSize) * 100, 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SEO -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">SEO Check</span>
                                                        @php 
                                                            $compSeo = $lastResult->details['comp_assets']['seo'] ?? []; 
                                                            $compSeoScore = 0;
                                                            if(!empty($compSeo['title'])) $compSeoScore += 33;
                                                            if(!empty($compSeo['description'])) $compSeoScore += 33;
                                                            if(!empty($compSeo['h1'])) $compSeoScore += 34;
                                                        @endphp
                                                        <span class="{{ $compSeoScore > 80 ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : ($compSeoScore > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                            {{ $compSeoScore }}%
                                                        </span>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <span title="Title Tag" class="flex-1 text-center py-1 rounded border {{ !empty($compSeo['title']) ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} text-[10px] font-semibold transition-colors">Title</span>
                                                        <span title="Meta Description" class="flex-1 text-center py-1 rounded border {{ !empty($compSeo['description']) ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} text-[10px] font-semibold transition-colors">Desc</span>
                                                        <span title="H1 Tag" class="flex-1 text-center py-1 rounded border {{ !empty($compSeo['h1']) ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} text-[10px] font-semibold transition-colors">H1</span>
                                                    </div>
                                                </div>

                                                <!-- Accessibility -->
                                                <div>
                                                    <div class="flex justify-between text-xs text-slate-400 mb-2.5">
                                                        <span class="font-medium text-slate-300">Accessibility (Img Alt)</span>
                                                        @php $compAlly = $lastResult->details['comp_assets']['ally']['score'] ?? 0; @endphp
                                                        <span class="{{ $compAlly > 80 ? 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]' : ($compAlly > 50 ? 'text-yellow-400' : 'text-red-400') }} font-bold">
                                                            {{ $compAlly }}%
                                                        </span>
                                                    </div>
                                                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                                        <div class="h-full rounded-full transition-all duration-1000 {{ $compAlly > 80 ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : ($compAlly > 50 ? 'bg-gradient-to-r from-yellow-500 to-yellow-400' : 'bg-gradient-to-r from-red-500 to-red-400') }}"
                                                            style="width: {{ $compAlly }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 bg-slate-900/60 border-t border-white/5 flex justify-between items-center text-sm relative z-10 backdrop-blur-md">
                                <span class="text-slate-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Updated {{ $lastResult->created_at->diffForHumans() }}
                                </span>
                                @if($winner === 'me')
                                    <span class="text-emerald-400 flex items-center gap-2 font-bold px-3 py-1 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        You're Faster!
                                    </span>
                                @elseif($winner === 'them')
                                    <span class="text-red-400 flex items-center gap-2 font-bold px-3 py-1 bg-red-500/10 rounded-full border border-red-500/20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                        They're Faster
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="p-12 text-center relative z-10 flex-1 flex flex-col items-center justify-center">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-purple-500/10 to-transparent flex items-center justify-center mx-auto mb-6 border border-purple-500/20 relative group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute inset-0 rounded-2xl bg-purple-500/20 animate-ping opacity-20"></div>
                                    <svg class="w-10 h-10 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-white mb-2">Ready to Race</h4>
                                <p class="text-slate-400 mb-8 text-sm max-w-xs mx-auto">Click below to start the first speed and SEO comparison.</p>
                                <form method="POST" action="{{ route('benchmarks.scan', $competitor) }}" class="w-full max-w-xs">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center justify-center bg-slate-800 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-xl border border-white/5 hover:border-purple-500 transition-all hover:shadow-[0_0_20px_rgba(168,85,247,0.4)] group-hover:bg-purple-600/20 group-hover:text-purple-300">
                                        Run First Scan
                                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-24 bg-slate-900/40 backdrop-blur-xl rounded-2xl border border-dashed border-white/10 relative overflow-hidden group hover:border-purple-500/30 transition-colors duration-500 md:col-span-2">
                        <div class="absolute inset-0 bg-gradient-to-b from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-purple-500/20 to-fuchsia-500/20 flex items-center justify-center mx-auto mb-6 border border-purple-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-[0_0_30px_rgba(168,85,247,0.1)]">
                            <svg class="w-12 h-12 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400 mb-3">No Competitors Yet</h3>
                        <p class="text-slate-400 text-lg max-w-md mx-auto">Add your first competitor using the form above to start benchmarking your store's performance against industry leaders.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>