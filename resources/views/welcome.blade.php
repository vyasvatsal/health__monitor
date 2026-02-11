<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Store Health Monitor | Enterprise Grade</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&family=jetbrains-mono:400,500&display=swap"
        rel="stylesheet" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --obsidian-bg: #020617;
            /* Slate 950 */
            --obsidian-surface: #0f172a;
            /* Slate 900 */
            --obsidian-card: #1e293b;
            /* Slate 800 */
            --accent-primary: #10b981;
            /* Emerald 500 */
            --accent-secondary: #3b82f6;
            /* Blue 500 */
            --border-color: rgba(255, 255, 255, 0.08);
            /* Consistent border */
        }

        html {
            scroll-padding-top: 100px;
            /* Fix for sticky nav anchoring overlap */
        }

        body {
            background-color: var(--obsidian-bg);
            font-family: 'Outfit', sans-serif;
            color: #e2e8f0;
            /* Slate 200 */
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* UI Components */
        .glass-panel {
            background: rgba(2, 6, 23, 0.9);
            /* More opaque to prevent content bleed */
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .tech-grid {
            background-size: 40px 40px;
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            mask-image: radial-gradient(circle at center, black 0%, transparent 80%);
        }

        /* Animations */
        @keyframes scanline {
            0% {
                background-position: 0% 0%;
            }

            100% {
                background-position: 0% 100%;
            }
        }

        .scanline-overlay {
            background: linear-gradient(to bottom, transparent 50%, rgba(16, 185, 129, 0.05) 51%, transparent 52%);
            background-size: 100% 4px;
            animation: scanline 10s linear infinite;
            pointer-events: none;
        }

        .text-glow {
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }

        .caret-blink {
            animation: blink 1s step-end infinite;
            border-right: 2px solid var(--accent-primary);
        }

        @keyframes blink {

            0%,
            100% {
                border-color: transparent
            }

            50% {
                border-color: var(--accent-primary);
            }
        }

        /* Utilities */
        .noise-bg {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.05'/%3E%3C/svg%3E");
        }
    </style>
</head>

<body class="antialiased overflow-x-hidden selection:bg-emerald-500/30 selection:text-emerald-200">

    <!-- Background Layers -->
    <div
        class="fixed inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-[#020617] to-[#020617] -z-20">
    </div>
    <div class="fixed inset-0 tech-grid -z-10"></div>
    <div class="fixed inset-0 noise-bg -z-10 pointer-events-none mix-blend-overlay"></div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-panel border-b-0 border-b-white/5 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="font-bold text-xl tracking-tight text-white">Health<span
                                class="text-emerald-500">Monitor</span></span>
                        <span
                            class="text-[10px] font-mono font-medium text-emerald-500/80 bg-emerald-500/5 px-1.5 py-0.5 rounded border border-emerald-500/10 uppercase tracking-wider">Enterprise</span>
                    </div>
                </div>

                <!-- Desktop Links -->
                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-400">
                    <a href="#features" class="hover:text-white transition-colors">Platform</a>
                    <a href="#compliance" class="hover:text-white transition-colors">Compliance</a>
                    <a href="#pricing" class="hover:text-white transition-colors">Pricing</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-medium text-white hover:text-emerald-400 transition">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                                @csrf
                                <button type="submit"
                                    class="text-sm font-medium text-slate-400 hover:text-rose-400 transition border border-transparent hover:border-rose-500/20 px-3 py-1.5 rounded-md">
                                    Log out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-medium text-slate-300 hover:text-white transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="text-sm bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-lg font-bold transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_25px_rgba(16,185,129,0.5)] border border-emerald-500/50">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Layout -->
    <main class="relative pt-32 pb-24">

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- Hero Content (5 cols) -->
                <div class="lg:col-span-5 space-y-8 flex flex-col items-center lg:items-start text-center lg:text-left">

                    <!-- Status Badge -->
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/50 border border-emerald-500/20 text-emerald-400 text-xs font-mono font-medium animate-fade-in">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        SYSTEM OPERATIONAL
                    </div>

                    <!-- Headline -->
                    <h1 class="text-5xl sm:text-6xl font-bold text-white leading-[1.1] tracking-tight">
                        Infrastructure <br />
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 text-glow">Intelligence</span>
                    </h1>

                    <p class="text-lg text-slate-400 leading-relaxed max-w-lg">
                        AI-driven observability for high-scale e-commerce. Detect anomalies, predict latency, and
                        auto-scale before your customers notice.
                    </p>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-4">
                        <a href="{{ route('register') }}"
                            class="group relative px-8 py-4 bg-white text-slate-950 rounded-lg font-bold text-base transition-all hover:bg-slate-100 flex items-center justify-center gap-2 overflow-hidden">
                            <span class="relative z-10">Start Monitoring Free</span>
                            <svg class="w-4 h-4 relative z-10 transition-transform group-hover:translate-x-1"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                        <a href="#features"
                            class="px-8 py-4 rounded-lg bg-slate-800/50 text-white font-semibold hover:bg-slate-800 transition border border-white/10 flex items-center justify-center">
                            Documentation
                        </a>
                    </div>
                </div>

                <!-- Dashboard Mockup (7 cols) -->
                <div class="lg:col-span-7 relative w-full perspective-[2000px]">
                    <!-- Glow Logic -->
                    <div
                        class="absolute -inset-1 bg-gradient-to-tr from-emerald-500/20 to-blue-500/20 rounded-xl blur-2xl opacity-50">
                    </div>

                    <!-- Main Window -->
                    <div
                        class="relative bg-[#0B1120] border border-white/10 rounded-xl shadow-2xl overflow-hidden transform rotate-y-[-2deg] rotate-x-[2deg] transition-transform duration-500 hover:rotate-0">

                        <!-- Window Title Bar -->
                        <div
                            class="bg-slate-900/90 border-b border-white/5 px-4 py-3 flex items-center justify-between">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-slate-700/50"></div>
                                <div class="w-3 h-3 rounded-full bg-slate-700/50"></div>
                                <div class="w-3 h-3 rounded-full bg-slate-700/50"></div>
                            </div>
                            <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">
                                Health_Monitor_Dashboard.exe</div>
                            <div class="w-12"></div> <!-- Spacer -->
                        </div>

                        <!-- Dashboard Grid -->
                        <div class="p-6 grid grid-cols-12 gap-6 bg-[#0B1120]">

                            <!-- Chart Widget (8 cols) -->
                            <div
                                class="col-span-8 bg-slate-900/30 border border-white/5 rounded-lg p-5 relative overflow-hidden group">
                                <div class="flex justify-between items-center mb-6">
                                    <div>
                                        <div class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">
                                            Total Requests</div>
                                        <div class="text-2xl font-bold text-white font-mono">24,592<span
                                                class="text-emerald-500 text-sm ml-2">↑ 12%</span></div>
                                    </div>
                                    <div class="flex gap-1">
                                        <div class="w-1 h-3 bg-emerald-500/20 rounded-sm"></div>
                                        <div class="w-1 h-3 bg-emerald-500/40 rounded-sm"></div>
                                        <div class="w-1 h-3 bg-emerald-500 rounded-sm"></div>
                                    </div>
                                </div>
                                <!-- CSS Chart -->
                                <div class="h-24 w-full flex items-end gap-1 opacity-80">
                                    <div class="w-[6%] h-[40%] bg-emerald-500/10 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[60%] bg-emerald-500/20 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[55%] bg-emerald-500/15 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[80%] bg-emerald-500/30 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[70%] bg-emerald-500/20 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[90%] bg-emerald-500/40 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[85%] bg-emerald-500/30 rounded-t-sm"></div>
                                    <div
                                        class="w-[6%] h-[100%] bg-emerald-500/50 rounded-t-sm border-t-2 border-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.3)]">
                                    </div>
                                    <div class="w-[6%] h-[50%] bg-emerald-500/10 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[40%] bg-emerald-500/10 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[30%] bg-emerald-500/10 rounded-t-sm"></div>
                                    <div class="w-[6%] h-[20%] bg-emerald-500/10 rounded-t-sm"></div>
                                </div>
                                <div class="absolute inset-0 scanline-overlay opacity-30"></div>
                            </div>

                            <!-- Status Widget (4 cols) -->
                            <div class="col-span-4 space-y-4">
                                <div class="bg-slate-900/30 border border-white/5 rounded-lg p-4">
                                    <div class="text-[10px] text-slate-500 font-bold uppercase mb-3">Service Health
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-300">Database</span>
                                            <span class="flex items-center gap-1.5 text-emerald-400">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></span>
                                                OK
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-300">Cache</span>
                                            <span class="flex items-center gap-1.5 text-emerald-400">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></span>
                                                OK
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-300">Queue</span>
                                            <span class="flex items-center gap-1.5 text-blue-400">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_5px_rgba(59,130,246,0.5)]"></span>
                                                BUSY
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-900/30 border border-white/5 rounded-lg p-4">
                                    <div class="text-[10px] text-slate-500 font-bold uppercase mb-1">Latency</div>
                                    <div class="text-2xl font-mono text-white font-bold">24ms</div>
                                </div>
                            </div>

                            <!-- Terminal Widget (12 cols) -->
                            <div
                                class="col-span-12 bg-black/50 border border-white/5 rounded-lg p-4 font-mono text-[11px] h-32 overflow-hidden relative">
                                <div class="text-slate-500 mb-1 border-b border-white/5 pb-1 flex justify-between">
                                    <span>Terminal</span>
                                    <span class="text-emerald-500/50">● Connected</span>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-emerald-600/60">> init_sequence --force</div>
                                    <div class="text-slate-400">> Loading health_modules... [OK]</div>
                                    <div class="text-slate-400">> Checking connectivity... [OK]</div>
                                    <div class="text-blue-400">> Starting anomaly_detection_engine...</div>
                                    <div class="text-white caret-blink">> Analyzing packet_stream_042...</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Ticker -->
        <div class="mt-24 border-y border-white/5 bg-slate-900/30 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-white/5">
                    <div class="p-2">
                        <div class="text-3xl font-bold text-white mb-1">99.99%</div>
                        <div class="text-xs text-slate-500 font-mono uppercase tracking-wider">Uptime</div>
                    </div>
                    <div class="p-2">
                        <div class="text-3xl font-bold text-white mb-1">&lt;50ms</div>
                        <div class="text-xs text-slate-500 font-mono uppercase tracking-wider">Latency</div>
                    </div>
                    <div class="p-2">
                        <div class="text-3xl font-bold text-white mb-1">24/7</div>
                        <div class="text-xs text-slate-500 font-mono uppercase tracking-wider">Monitoring</div>
                    </div>
                    <div class="p-2">
                        <div class="text-3xl font-bold text-white mb-1">10k+</div>
                        <div class="text-xs text-slate-500 font-mono uppercase tracking-wider">Incidents Prevented</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div
                    class="group p-8 rounded-2xl bg-slate-900/20 border border-white/5 hover:bg-slate-800/40 hover:border-white/10 transition-all duration-300">
                    <div
                        class="w-12 h-12 bg-emerald-500/10 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3 group-hover:text-emerald-400 transition-colors">
                        Predictive Scaling</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Our algorithms forecast traffic spikes before they happen, auto-scaling your infrastructure to
                        zero latency.
                    </p>
                </div>

                <!-- Card 2 -->
                <div
                    class="group p-8 rounded-2xl bg-slate-900/20 border border-white/5 hover:bg-slate-800/40 hover:border-white/10 transition-all duration-300">
                    <div
                        class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3 group-hover:text-blue-400 transition-colors">Full-Stack
                        Tracing</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Trace every request from the edge to the database. Pinpoint exact lines of code causing
                        production bottlenecks.
                    </p>
                </div>

                <!-- Card 3 -->
                <div
                    class="group p-8 rounded-2xl bg-slate-900/20 border border-white/5 hover:bg-slate-800/40 hover:border-white/10 transition-all duration-300">
                    <div
                        class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3 group-hover:text-purple-400 transition-colors">Security
                        Auditing</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Real-time vulnerability scanning and dependency auditing. Stay compliant with ISO and SOC2
                        automatically.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-white/5 bg-[#020617] pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
                <div class="flex items-center gap-2 mb-6">
                    <span class="font-bold text-lg text-white">HealthMonitor</span>
                    <span
                        class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded border border-white/5">ENTERPRISE</span>
                </div>
                <div class="flex gap-6 text-sm text-slate-500 mb-8">
                    <a href="#" class="hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition">Terms of Service</a>
                    <a href="#" class="hover:text-white transition">Status</a>
                    <a href="#" class="hover:text-white transition">Contact Support</a>
                </div>
                <div class="text-slate-600 text-xs">
                    &copy; {{ date('Y') }} AI Store Health Monitor Inc. All rights reserved.
                </div>
            </div>
        </footer>

    </main>
</body>

</html>