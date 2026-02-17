<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Status | Health Monitor</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
        }
    </style>
</head>

<body class="antialiased min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-[#1e293b] border-b border-slate-700 py-6 shadow-lg">
        <div class="max-w-4xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-tr from-emerald-500 to-cyan-500 p-2 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">System Status</h1>
                    <p class="text-xs text-slate-400">Real-time performance & availability</p>
                </div>
            </div>
            <!-- Removed Admin Login for cleaner public look -->
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-3xl mx-auto px-6 py-10">

            <!-- Overall Status Banner -->
            <div class="rounded-xl p-6 mb-10 text-center shadow-2xl relative overflow-hidden group
                @if($systemStatus == 'Operational') bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 
                @elseif($systemStatus == 'Maintenance') bg-blue-500/10 border border-blue-500/20 text-blue-400
                @else bg-red-500/10 border border-red-500/20 text-red-400 @endif">

                <div class="relative z-10 flex flex-col items-center gap-4">
                    @if($systemStatus == 'Operational')
                        <svg class="w-16 h-16 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-3xl font-bold text-white">All Systems Operational</h2>
                    @else
                        <svg class="w-16 h-16 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h2 class="text-3xl font-bold text-white">{{ $systemStatus }}</h2>
                    @endif
                </div>
            </div>

            <!-- Active Incidents -->
            @if($activeIncidents->count() > 0)
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-white mb-4">Active Incidents</h3>
                    <div class="space-y-4">
                        @foreach($activeIncidents as $incident)
                            <div class="bg-[#1e293b] rounded-lg border border-l-4 p-6 
                                                @if($incident->severity == 'critical') border-l-red-500 border-slate-700
                                                @elseif($incident->severity == 'maintenance') border-l-blue-500 border-slate-700
                                                @else border-l-yellow-500 border-slate-700 @endif">
                                <h4 class="text-lg font-bold text-white flex justify-between">
                                    {{ $incident->title }}
                                    <span
                                        class="text-xs px-2 py-1 rounded bg-slate-800 text-slate-300 uppercase tracking-wider">{{ $incident->status }}</span>
                                </h4>
                                <p class="text-slate-400 mt-2 text-sm">{{ $incident->description }}</p>
                                <p class="text-slate-500 text-xs mt-4">{{ $incident->created_at->format('M d, H:i T') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Uptime Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-[#1e293b] p-6 rounded-lg border border-slate-700">
                    <h4 class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">30-Day Uptime</h4>
                    <div class="text-3xl font-bold text-white">{{ $uptime30d }}%</div>
                </div>
                <div class="bg-[#1e293b] p-6 rounded-lg border border-slate-700">
                    <h4 class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">Current Response Time
                    </h4>
                    <div class="text-3xl font-bold text-white">~45ms</div>
                </div>
            </div>

            <!-- Components list -->
            <div class="bg-[#1e293b] rounded-lg border border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-slate-700 bg-slate-800/50">
                    <h4 class="text-white font-bold">System Metrics</h4>
                </div>
                <div class="divide-y divide-slate-700">
                    @foreach($components as $component)
                        <div class="p-4 flex justify-between items-center group hover:bg-slate-800/30 transition-colors">
                            <div>
                                <p class="text-slate-200 font-medium">{{ $component['name'] }}</p>
                                <p class="text-xs text-slate-500">Updated {{ $component['updated_at'] }}</p>
                            </div>
                            @if($component['status'] == 'Operational')
                                <span class="text-emerald-400 text-sm font-medium flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Operational
                                </span>
                            @else
                                <span class="text-red-400 text-sm font-medium flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Outage
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </main>

    <footer class="py-8 text-center text-slate-600 text-sm">
        &copy; {{ date('Y') }} Service Status Page. Powered by <a href="#"
            class="hover:text-slate-400 transition-colors">AI Health Monitor</a>.
    </footer>
</body>

</html>