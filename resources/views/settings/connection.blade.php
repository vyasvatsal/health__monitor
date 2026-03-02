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
                        <div>
                            <label for="project_select" class="block text-sm font-medium text-slate-400">Select Project to
                                Configure</label>
                            <p class="text-xs text-slate-500 mt-1">Each project requires its own unique API and Tracking Keys.</p>
                        </div>

                        <form action="{{ route('settings.connection') }}" method="GET" class="w-full sm:w-auto">
                            <select id="project_select" name="store_id" onchange="this.form.submit()"
                                class="w-full sm:w-64 bg-slate-900 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                                @foreach($stores as $s)
                                    <option value="{{ $s->id }}" {{ $store->id === $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} ({{ parse_url($s->url, PHP_URL_HOST) ?? $s->url }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
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
                                        Install the SDK via Composer
                                    </h4>
                                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-800">
                                        <code
                                            class="text-emerald-400 font-mono text-sm">composer require aihealth/laravel-sdk</code>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div>
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                                        Configure your .env file
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-3">Add this DSN to your <code class="text-white">.env</code>
                                        file. This instantly enables active tracking for Server Errors, Exceptions, and System Logs.
                                    </p>
                                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-800 overflow-x-auto relative group">
                                        <pre><code class="text-emerald-300 font-mono text-sm">AIHEALTH_DSN="http://{{ $store->public_key }}@{{ request()->getHost() . ':' . request()->getPort() }}/{{ $store->id }}"
                                AIHEALTH_SEND_EXCEPTIONS=true
                                AIHEALTH_SEND_LOGS=true</code></pre>
                                    </div>
                                </div>

                                <!-- Step 3 (Optional) -->
                                <div>
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-slate-700 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">#</span>
                                        Monitor Server Health (Optional)
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-3">To monitor CPU Load, Memory Limits, and DB Connectivity,
                                        register the heartbeat command in your <code>routes/console.php</code> file.</p>
                                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-800 overflow-x-auto">
                                        <pre><code class="text-blue-300 font-mono text-sm">use Illuminate\Support\Facades\Schedule;

                                Schedule::command('aihealth:send-health')->everyFiveMinutes();</code></pre>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div>
                                    <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                        <span
                                            class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">3</span>
                                        Inject the Web RUM Tracker
                                    </h4>
                                    <p class="text-sm text-slate-400 mb-3">To enable Frontend Tracking (Core Web Vitals, CTA Clicks,
                                        User Flow), add our Blade directive right before the <code
                                            class="text-white">&lt;/head&gt;</code> tag in your main layout file (<code
                                            class="text-white">app.blade.php</code>).</p>
                                    <div
                                        class="bg-slate-900 rounded-lg p-4 border border-slate-800 flex justify-between items-center group">
                                        <code class="text-purple-400 font-bold font-mono text-sm">&#64;aihealth</code>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-3 pt-3 border-t border-slate-700/50"><strong>Bonus:</strong>
                                        To
                                        explicitly track any button conversions, simply add the attribute <code
                                            class="text-indigo-400">data-aihealth-cta="button_name"</code> to your HTML tags.</p>
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