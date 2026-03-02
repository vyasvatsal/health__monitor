<x-app-layout>
    <x-slot name="header">
        {{ __('Incident Details') }}
    </x-slot>

    <div class="py-8 flex-1 flex flex-col">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-8">

            <div class="flex justify-between items-center">
                <h2
                    class="font-bold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400 leading-tight">
                    Incident Details
                </h2>
                <a href="{{ route('incidents.index') }}"
                    class="flex items-center gap-2 text-sm text-slate-300 hover:text-white bg-slate-800/80 hover:bg-slate-700 px-4 py-2 rounded-xl transition-all border border-white/5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Incidents
                </a>
            </div>

            @if(session('success'))
                <div
                    class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-xl flex items-center gap-4 relative animate-fade-in-down shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Success</p>
                        <p class="text-sm opacity-80">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <div
                        class="bg-slate-900/40 backdrop-blur-xl overflow-hidden rounded-2xl border border-white/5 shadow-2xl relative">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/30 to-transparent opacity-50">
                        </div>

                        <div class="p-8">
                            <div class="mb-6 pb-6 border-b border-white/5">
                                <h3 class="text-3xl font-black text-white mb-2">{{ $incident->title }}</h3>
                                <p class="text-sm text-slate-400 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Reported {{ $incident->created_at->format('F j, Y g:i A') }}
                                    <span class="opacity-50">•</span>
                                    <span>{{ $incident->created_at->diffForHumans() }}</span>
                                </p>
                            </div>

                            <div class="prose prose-invert max-w-none prose-p:text-slate-300 prose-headings:text-white">
                                <h4 class="text-lg font-bold mb-4 flex items-center gap-2 text-white">
                                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                    Description
                                </h4>
                                <div
                                    class="bg-slate-950/50 p-6 rounded-xl border border-white/5 text-slate-300 whitespace-pre-wrap leading-relaxed">
                                    {{ $incident->description }}</div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-white/5 flex gap-4">
                                <a href="{{ route('incidents.edit', $incident) }}"
                                    class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-[0_0_15px_rgba(37,99,235,0.3)] transition-all hover:scale-[1.02]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Edit Incident
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Status -->
                <div class="space-y-8">
                    <!-- Status Card -->
                    <div class="bg-slate-900/40 backdrop-blur-xl rounded-2xl border border-white/5 shadow-xl p-6">
                        <h4
                            class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">
                            Current Status</h4>

                        <div class="space-y-6">
                            <div>
                                <p class="text-xs text-slate-500 mb-2 uppercase tracking-wider font-semibold">Severity
                                </p>
                                @php
                                    $severityColors = [
                                        'critical' => 'bg-red-500/10 text-red-400 border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.2)]',
                                        'major' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20 shadow-[0_0_15px_rgba(249,115,22,0.2)]',
                                        'minor' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                        'maintenance' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                    ];
                                    $color = $severityColors[$incident->severity] ?? 'bg-slate-500/10 text-slate-400 border border-slate-500/20';
                                @endphp
                                <div
                                    class="inline-flex {{ $color }} px-4 py-2 rounded-lg font-black uppercase tracking-widest text-sm items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-current"></span>
                                    {{ $incident->severity }}
                                </div>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500 mb-2 uppercase tracking-wider font-semibold">Stage</p>
                                @php
                                    $statusColors = [
                                        'open' => 'bg-red-500/10 text-red-400 border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.2)]',
                                        'investigating' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-[0_0_15px_rgba(168,85,247,0.2)]',
                                        'identified' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                        'monitoring' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                        'resolved' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.2)]',
                                    ];
                                    $statusColor = $statusColors[$incident->status] ?? 'bg-slate-500/10 text-slate-400 border border-slate-500/20';
                                @endphp
                                <div
                                    class="inline-flex {{ $statusColor }} px-4 py-2 rounded-lg font-black uppercase tracking-widest text-sm items-center gap-2">
                                    @if($incident->status !== 'resolved')
                                        <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                    {{ str_replace('_', ' ', $incident->status) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-slate-900/40 backdrop-blur-xl rounded-2xl border border-white/5 shadow-xl p-6">
                        <h4
                            class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">
                            Live Timeline</h4>

                        <div
                            class="relative pl-4 space-y-8 before:absolute before:inset-y-0 before:left-[7px] before:w-[2px] before:bg-gradient-to-b before:from-red-500/50 before:via-white/10 before:to-transparent">
                            <!-- Created -->
                            <div class="relative">
                                <div
                                    class="absolute -left-[24px] bg-slate-900 text-red-500 rounded-full border-2 border-red-500/50 p-1 mt-0.5 z-10">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-sm">Incident Reported</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $incident->created_at->format('M j, Y • g:i A') }}</p>
                                </div>
                            </div>

                            @if($incident->resolved_at)
                                <!-- Resolved -->
                                <div class="relative">
                                    <div
                                        class="absolute -left-[24px] bg-slate-900 text-emerald-500 rounded-full border-2 border-emerald-500/50 p-1 mt-0.5 z-10 shadow-[0_0_10px_rgba(16,185,129,0.3)]">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-emerald-400 text-sm">Resolved</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $incident->resolved_at->format('M j, Y • g:i A') }}</p>
                                    </div>
                                </div>
                            @elseif($incident->status === 'monitoring')
                                <div class="relative">
                                    <div
                                        class="absolute -left-[24px] bg-slate-900 text-blue-500 rounded-full border-2 border-blue-500/50 p-1 mt-0.5 z-10 shadow-[0_0_10px_rgba(59,130,246,0.3)]">
                                        <span class="block w-3 h-3 rounded-full bg-current animate-ping"></span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-blue-400 text-sm">Monitoring</p>
                                        <p class="text-xs text-slate-500 mt-1">Currently observing for stability</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>