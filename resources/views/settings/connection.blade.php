@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight">
        {{ __('Connection Guide') }}
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
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    SDK Connection Guide
                </h3>
                <p class="mt-2 text-slate-400">Install the Laravel SDK to unlock Real User Monitoring (RUM), Error Tracking,
                    and AI Performance Analytics.</p>
            </div>

            @if($stores->isEmpty())
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-6 text-center">
                    <p class="text-red-400">No projects found. Please create a project first.</p>
                </div>
            @else

                    <!-- Project Selector Dropdown -->
                    <div
                        class="bg-[#1e293b] border border-white/5 rounded-lg p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <form action="{{ route('settings.connection') }}" method="GET" id="projectSelectorForm">
                                <select name="store_id" onchange="document.getElementById('projectSelectorForm').submit()"
                                    class="bg-slate-800 border-slate-700 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-64 p-2.5">
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}" {{ $store->id == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            @if($store->last_seen_at && $store->last_seen_at->gt(now()->subMinutes(15)))
                                <span
                                    class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Live Status
                                </span>
                            @else
                                <span
                                    class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-500/10 border border-slate-500/20 text-slate-400 text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                    Waiting for Traffic...
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Keys Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- API Key -->
                        <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6">
                            <h4 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Backend API Key (DSN)
                            </h4>
                            <p class="text-sm text-slate-400 mb-4">Required to send Server Errors and Backend Logs.</p>
                            <div class="relative">
                                <input type="text" readonly value="{{ $store->api_key }}"
                                    class="w-full bg-slate-800 border-slate-700 rounded-lg text-emerald-400 font-mono text-sm py-3 px-4 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <!-- RUM Key -->
                        <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6">
                            <h4 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Frontend Private Tracking Key
                            </h4>
                            <p class="text-sm text-slate-400 mb-4">Required for native Javascript RUM, Load Speeds, and CTA
                                Tracking.</p>
                            <div class="relative">
                                <input type="text" readonly
                                    value="{{ $store->private_tracking_key ?? 'Key Not Generated - Save Project to Generate' }}"
                                    class="w-full bg-slate-800 border-slate-700 rounded-lg text-purple-400 font-mono text-sm py-3 px-4 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>

                    </div>

                    <!-- Installation Instructions -->
                    <div x-data="{ activeTab: 'laravel' }"
                        class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                        <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-white">How to Install</h3>
                                <p class="text-sm text-slate-400 mt-1">Select your project's technology stack to view the setup
                                    guide.</p>
                            </div>

                            <!-- Language/Framework Selector Tabs -->
                            <div
                                class="flex items-center gap-2 bg-slate-900/50 p-1.5 rounded-xl border border-white/5 overflow-x-auto no-scrollbar">
                                <button @click="activeTab = 'laravel'"
                                    :class="activeTab === 'laravel' ? 'bg-red-500/10 text-red-400 border border-red-500/20 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all whitespace-nowrap">
                                    <i class="fab fa-laravel text-lg"></i> Laravel
                                </button>
                                <button @click="activeTab = 'nodejs'"
                                    :class="activeTab === 'nodejs' ? 'bg-green-500/10 text-green-400 border border-green-500/20 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all whitespace-nowrap">
                                    <i class="fab fa-node-js text-lg"></i> Node.js
                                </button>
                                <button @click="activeTab = 'nextjs'"
                                    :class="activeTab === 'nextjs' ? 'bg-slate-700 text-white border border-slate-600 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all whitespace-nowrap">
                                    <svg class="w-4 h-4" viewBox="0 0 128 128" fill="currentColor">
                                        <path
                                            d="M64 0C28.7 0 0 28.7 0 64s28.7 64 64 64c35.3 0 64-28.7 64-64S99.3 0 64 0zm33.3 93.9L53.7 41.6h-9.9v42.8h8V52l40.4 48.7c-8 6.9-18.4 11.2-30.5 11.2C38 111.9 16 89.9 16 63.9s22-48 48-48 48 22 48 48c0 10.3-3.3 19.9-8.7 28zm6.5-12.7h-8V41.6h8v39.6z" />
                                    </svg>
                                    Next.js
                                </button>
                            </div>
                        </div>

                        <div class="p-6 relative">

                            <!-- Laravel Content -->
                            <div x-show="activeTab === 'laravel'" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                                <!-- Step 1 -->
                                <div>
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span>
                                        Install the SDK
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-3">
                                        Because the SDK is currently in private development, you need to add our repository to your
                                        <code class="text-white">composer.json</code> before installing.
                                    </p>

                                    <div class="space-y-4">
                                        <!-- Sub-step A -->
                                        <!-- <div class="bg-slate-900 rounded-lg p-1 border border-slate-800">
                                                                                            <div class="px-3 py-1.5 border-b border-slate-800 flex items-center justify-between">
                                                                                                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">A.
                                                                                                    Register Repository</span>
                                                                                            </div>
                                                                                            <div class="p-3">
                                                                                                <pre><code class="text-indigo-400 font-mono text-xs">composer config repositories.aihealth vcs https://github.com/vyasvatsal/aihealth-laravel-sdk.git</code></pre>
                                                                                            </div>
                                                                                        </div> -->

                                        <!-- Sub-step B -->
                                        <div class="bg-slate-900 rounded-lg p-1 border border-slate-800">
                                            <div class="px-3 py-1.5 border-b border-slate-800 flex items-center justify-between">
                                                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">
                                                    Require Package</span>
                                            </div>
                                            <div class="p-3">
                                                <code
                                                    class="text-emerald-400 font-mono text-sm">composer require aihealth/laravel-monitor</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div>
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                                        Configure your Environment
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-6">
                                        Open your project's <code class="text-white">.env</code> file and add the following keys.
                                        These connect your application to the Health Monitor securely.
                                    </p>

                                    <!-- Mini .env Editor Visual -->
                                    <div class="bg-[#0f172a] rounded-xl border border-white/10 overflow-hidden mb-8 shadow-2xl">
                                        <div class="flex items-center gap-1.5 px-4 py-3 bg-slate-900/80 border-b border-white/5">
                                            <div class="flex gap-1.5 mr-4">
                                                <div class="w-3 h-3 rounded-full bg-slate-700"></div>
                                                <div class="w-3 h-3 rounded-full bg-slate-700"></div>
                                                <div class="w-3 h-3 rounded-full bg-slate-700"></div>
                                            </div>
                                            <span class="text-[11px] font-mono text-slate-500 uppercase tracking-widest">.env</span>
                                        </div>
                                        <div
                                            class="p-6 font-mono text-sm leading-relaxed overflow-x-auto selection:bg-indigo-500/30">
                                            <div class="flex gap-4">
                                                <div class="shrink-0 text-slate-600 text-right select-none w-4">
                                                    <div>1</div>
                                                    <div>2</div>
                                                    <div>3</div>
                                                    <div>4</div>
                                                </div>
                                                <div class="whitespace-nowrap">
                                                    <div class="group py-0.5">
                                                        <span class="text-emerald-400">AIHEALTH_DSN</span><span
                                                            class="text-slate-500">=</span><span
                                                            class="text-indigo-300">"{{ (request()->isSecure() ? 'https' : 'http') . '://' . $store->api_key . '@' . request()->getHost() . (request()->getPort() != 80 && request()->getPort() != 443 ? ':' . request()->getPort() : '') . '/' . $store->id }}"</span>
                                                    </div>
                                                    <div class="group py-0.5">
                                                        <span class="text-purple-400">AIHEALTH_PRIVATE_TRACKING_KEY</span><span
                                                            class="text-slate-500">=</span><span
                                                            class="text-indigo-300">"{{ $store->private_tracking_key ?? 'key_not_generated' }}"</span>
                                                    </div>
                                                    <div class="group py-0.5 opacity-50">
                                                        <span class="text-slate-400">AIHEALTH_ENVIRONMENT</span><span
                                                            class="text-slate-500">=</span><span
                                                            class="text-indigo-300">"production"</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <div class="bg-slate-800/30 p-4 rounded-xl border border-white/5">
                                            <h5 class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1.5">
                                                Backend Security</h5>
                                            <p class="text-[11px] text-slate-400 leading-relaxed italic">Encrypts server-side
                                                exceptions, logs, and system metrics.</p>
                                        </div>
                                        <div class="bg-slate-800/30 p-4 rounded-xl border border-white/5">
                                            <h5 class="text-[10px] font-bold text-purple-400 uppercase tracking-widest mb-1.5">
                                                Frontend Security</h5>
                                            <p class="text-[11px] text-slate-400 leading-relaxed italic">Required to encrypt RUM
                                                data sent from user browsers.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div>
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">3</span>
                                        Enable Real User Monitoring (RUM)
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-6">
                                        Add the <code class="text-white">@aihealth</code> directive to your main layout file
                                        (<code class="text-white">app.blade.php</code>) right before the closing
                                        <code class="text-white">&lt;/head&gt;</code> tag.
                                    </p>

                                    <!-- Mini Code Editor Visual -->
                                    <div class="bg-[#0f172a] rounded-xl border border-white/10 overflow-hidden mb-8 shadow-2xl">
                                        <div class="flex items-center gap-1.5 px-4 py-3 bg-slate-900/80 border-b border-white/5">
                                            <div class="flex gap-1.5 mr-4">
                                                <div class="w-3 h-3 rounded-full bg-slate-700"></div>
                                                <div class="w-3 h-3 rounded-full bg-slate-700"></div>
                                                <div class="w-3 h-3 rounded-full bg-slate-700"></div>
                                            </div>
                                            <span
                                                class="text-[11px] font-mono text-slate-500 uppercase tracking-widest">resources/views/layouts/app.blade.php</span>
                                        </div>
                                        <div
                                            class="p-6 font-mono text-sm leading-relaxed overflow-x-auto selection:bg-indigo-500/30">
                                            <div class="flex gap-4">
                                                <div class="shrink-0 text-slate-600 text-right select-none w-4">
                                                    <div>1</div>
                                                    <div>2</div>
                                                    <div>3</div>
                                                    <div>4</div>
                                                    <div>5</div>
                                                </div>
                                                <div class="text-slate-300">
                                                    <div>&lt;<span class="text-indigo-400">head</span>&gt;</div>
                                                    <div class="pl-4 text-slate-500">... previous tags ...</div>
                                                    <div class="pl-4 py-1.5 group flex items-center gap-2">
                                                        <span class="text-purple-400 font-bold">@aihealth</span>
                                                        <span
                                                            class="text-[10px] bg-purple-500/20 text-purple-400 px-1.5 py-0.5 rounded border border-purple-500/20 animate-pulse">ADD
                                                            THIS HERE</span>
                                                    </div>
                                                    <div>&lt;/<span class="text-indigo-400">head</span>&gt;</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div
                                            class="bg-indigo-500/5 border border-indigo-500/10 rounded-xl p-4 transition-all hover:bg-indigo-500/10 hover:border-indigo-500/30">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                                    <i class="fas fa-bolt text-sm"></i>
                                                </div>
                                                <h5 class="text-sm font-bold text-white">Core Web Vitals</h5>
                                            </div>
                                            <p class="text-[11px] text-slate-400 leading-relaxed">Automatic measurement of LCP, CLS,
                                                and FCP for speed tracking.</p>
                                        </div>

                                        <div
                                            class="bg-indigo-500/5 border border-indigo-500/10 rounded-xl p-4 transition-all hover:bg-indigo-500/10 hover:border-indigo-500/30">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                                    <i class="fas fa-mouse-pointer text-sm"></i>
                                                </div>
                                                <h5 class="text-sm font-bold text-white">CTA Tracking</h5>
                                            </div>
                                            <p class="text-[11px] text-slate-400 leading-relaxed">Captures engagement on buttons and
                                                links to show user patterns.</p>
                                        </div>

                                        <div
                                            class="bg-indigo-500/5 border border-indigo-500/10 rounded-xl p-4 transition-all hover:bg-indigo-500/10 hover:border-indigo-500/30">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                                    <i class="fas fa-shield-alt text-sm"></i>
                                                </div>
                                                <h5 class="text-sm font-bold text-white">Zero-Delay Script</h5>
                                            </div>
                                            <p class="text-[11px] text-slate-400 leading-relaxed">Async Javascript that won't impact
                                                your page load performance.</p>
                                        </div>

                                        <div
                                            class="bg-indigo-500/5 border border-indigo-500/10 rounded-xl p-4 transition-all hover:bg-indigo-500/10 hover:border-indigo-500/30">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                                    <i class="fas fa-eye-slash text-sm"></i>
                                                </div>
                                                <h5 class="text-sm font-bold text-white">Privacy First</h5>
                                            </div>
                                            <p class="text-[11px] text-slate-400 leading-relaxed">Only activates if the Private
                                                Tracking Key is present in .env.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div class="relative">
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">4</span>
                                        Sync Routes for Web Crawler
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-6">
                                        To enable automatic page analysis and CTA discovery, you
                                        must synchronize your application's routes to the dashboard.
                                    </p>
                                    
                                    <!-- Terminal Visual -->
                                    <div class="bg-slate-900 rounded-xl border border-white/5 overflow-hidden shadow-xl mb-6 group">
                                        <div class="flex items-center justify-between px-4 py-2 bg-slate-800/50 border-b border-white/5">
                                            <div class="flex gap-1.5">
                                                <div class="w-2.5 h-2.5 rounded-full bg-red-500/20 border border-red-500/20"></div>
                                                <div class="w-2.5 h-2.5 rounded-full bg-amber-500/20 border border-amber-500/20"></div>
                                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/20 border border-emerald-500/20"></div>
                                            </div>
                                            <span class="text-[9px] uppercase tracking-widest font-bold text-slate-500">Terminal</span>
                                        </div>
                                        <div class="p-4 flex items-center justify-between">
                                            <code class="text-emerald-400 font-mono text-sm flex items-center gap-2">
                                                <span class="text-slate-600 select-none">$</span>
                                                php artisan aihealth:sync-routes
                                            </code>
                                            <i class="fas fa-terminal text-slate-700 group-hover:text-emerald-500/50 transition-colors"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 5 -->
                                <div class="relative pt-4">
                                    <div
                                        class="bg-emerald-500/5 border border-emerald-500/20 rounded-2xl p-6 shadow-[0_0_20px_rgba(16,185,129,0.05)] transition-all hover:shadow-[0_0_30px_rgba(16,185,129,0.1)]">
                                        <div class="flex items-center gap-4 mb-4">
                                            <div
                                                class="bg-emerald-500 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                                <i class="fas fa-rocket animate-bounce-slow"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold text-white">Instant Verification</h4>
                                                <p class="text-sm text-slate-400">Final Step: Run this to confirm everything is
                                                    connected.</p>
                                            </div>
                                        </div>
                                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-800 group relative">
                                            <div class="flex items-center justify-between">
                                                <code class="text-emerald-400 font-mono text-sm">php artisan aihealth:test</code>
                                                <span
                                                    class="text-[10px] text-slate-500 font-bold uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Run
                                                    in Terminal</span>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-4 text-center italic">This will send a test error, a
                                            test log, and sync your project name instantly.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Node.js Content (Placeholder) -->
                            <div x-show="activeTab === 'nodejs'" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0" style="display: none;"
                                class="space-y-6 text-center py-12">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500/20 to-transparent flex items-center justify-center mx-auto mb-4 border border-green-500/20">
                                    <i class="fab fa-node-js text-3xl text-green-400"></i>
                                </div>
                                <h4 class="text-xl font-bold text-white">Node.js / Express SDK</h4>
                                <p class="text-slate-400 max-w-md mx-auto">The official Node.js SDK is currently in development. You
                                    can still use our REST API directly to ingest data from Node.js applications.</p>
                                <button
                                    class="px-6 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg transition-colors border border-white/5 text-sm font-medium mt-4">
                                    View API Documentation
                                </button>
                            </div>

                            <!-- Next.js Content (Placeholder) -->
                            <div x-show="activeTab === 'nextjs'" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0" style="display: none;"
                                class="space-y-6 text-center py-12">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-500/20 to-transparent flex items-center justify-center mx-auto mb-4 border border-slate-500/20">
                                    <svg class="w-8 h-8 text-white" viewBox="0 0 128 128" fill="currentColor">
                                        <path
                                            d="M64 0C28.7 0 0 28.7 0 64s28.7 64 64 64c35.3 0 64-28.7 64-64S99.3 0 64 0zm33.3 93.9L53.7 41.6h-9.9v42.8h8V52l40.4 48.7c-8 6.9-18.4 11.2-30.5 11.2C38 111.9 16 89.9 16 63.9s22-48 48-48 48 22 48 48c0 10.3-3.3 19.9-8.7 28zm6.5-12.7h-8V41.6h8v39.6z" />
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold text-white">Next.js / React SDK</h4>
                                <p class="text-slate-400 max-w-md mx-auto">The Next.js Edge-compatible SDK & RUM Tracker is coming
                                    soon. Stay tuned for seamless Vercel integration.</p>
                            </div>

                        </div>
                    </div>
                </div>

            @endif

    </div>
    </div>
@endsection