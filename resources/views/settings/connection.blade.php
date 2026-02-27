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

            @if(!$store)
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-6 text-center">
                    <p class="text-red-400">No projects found. Please create a project first.</p>
                </div>
            @else

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
                <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
                    <div class="p-6 border-b border-white/5">
                        <h3 class="text-lg font-bold text-white">How to Install</h3>
                        <p class="text-sm text-slate-400 mt-1">Follow these 3 steps to connect your Laravel application to the
                            Health Monitor.</p>
                    </div>

                    <div class="p-6 space-y-8">

                        <!-- Step 1 -->
                        <div>
                            <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                <span
                                    class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span>
                                Install the SDK via Composer
                            </h4>
                            <div class="bg-slate-900 rounded-lg p-4 border border-slate-800">
                                <code
                                    class="text-emerald-400 font-mono text-sm">composer require aihealth/laravel-monitor</code>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div>
                            <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                <span
                                    class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                                Configure your .env file
                            </h4>
                            <p class="text-sm text-slate-400 mb-3">Add these exact keys to the <code
                                    class="text-white">.env</code> file of your target Laravel project.</p>
                            <div class="bg-slate-900 rounded-lg p-4 border border-slate-800 overflow-x-auto">
                                <pre><code class="text-blue-300 font-mono text-sm">AIHEALTH_ENDPOINT={{ url('/api/ingest') }}
                        AIHEALTH_RUM_ENDPOINT={{ url('/api/v1/metrics/track') }}

                        <span class="text-emerald-400">AIHEALTH_DSN={{ $store->api_key }}</span>
                        AIHEALTH_PROJECT_ID={{ $store->id }}

                        <span class="text-purple-400">AIHEALTH_PRIVATE_TRACKING_KEY={{ $store->private_tracking_key }}</span></code></pre>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div>
                            <h4 class="text-md font-bold text-white mb-3 flex items-center gap-2">
                                <span
                                    class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs">3</span>
                                Inject the RUM Tracker (Optional, for UI/Speed analytics)
                            </h4>
                            <p class="text-sm text-slate-400 mb-3">To enable Frontend Tracking (Core Web Vitals, CTA Clicks, JS
                                Load Time), add the Blade directive right before your <code
                                    class="text-white">&lt;/head&gt;</code> tag in your main layout file (e.g., <code
                                    class="text-white">resources/views/layouts/app.blade.php</code>).</p>
                            <div class="bg-slate-900 rounded-lg p-4 border border-slate-800">
                                <code class="text-purple-400 font-mono text-sm">&#64;aihealth</code>
                            </div>
                        </div>

                    </div>
                </div>

            @endif

        </div>
    </div>
@endsection