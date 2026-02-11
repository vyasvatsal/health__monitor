<x-app-layout>
    <x-slot name="header">Projects</x-slot>
    <div class="py-12 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col">
            
            <!-- Page Header -->
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="font-bold text-2xl text-white leading-tight tracking-tight">
                        {{ __('Project Dashboard') }}
                    </h2>
                    <p class="text-sm text-slate-400 mt-1 max-w-2xl">Manage your product inventory and monitor health status across all applications.</p>
                </div>
                <a href="{{ route('stores.create') }}"
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2 border border-emerald-500/20 group hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Project
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($stores as $store)
                    @php
                        // Calculate simple stats
                        $status = 'healthy';
                        $statusColor = 'emerald';
                        if ($store->incidents_count > 0) {
                            $status = 'critical'; 
                            $statusColor = 'red';
                        }
                    @endphp

                    <div class="group relative bg-[#1e293b]/80 backdrop-blur-xl rounded-2xl border border-white/5 p-6 hover:border-emerald-500/30 transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-900/20 overflow-hidden flex flex-col h-full">
                        <!-- Glass Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        <!-- Header -->
                        <div class="relative flex justify-between items-start mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
                                    <div class="w-6 h-6 rounded bg-{{ $statusColor }}-500 flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ substr($store->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">{{ $store->name }}</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-{{ $statusColor }}-500 animate-pulse"></div>
                                        <span class="text-xs text-slate-400 font-mono">{{ $status == 'healthy' ? 'Operational' : 'Issues Detected' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="px-2 py-1 bg-{{ $statusColor }}-500/10 text-{{ $statusColor }}-400 text-[10px] uppercase font-bold rounded border border-{{ $statusColor }}-500/20 tracking-wider">
                                    {{ $store->tier }}
                                </span>
                            </div>
                        </div>

                        <!-- Metrics Grid -->
                        <div class="relative grid grid-cols-2 gap-3 mb-6 mt-auto">
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-white/5 group-hover:bg-slate-900/70 transition-colors">
                                <span class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Monitors</span>
                                <span class="text-xl font-mono text-white">{{ $store->health_checks_count }}</span>
                            </div>
                            <div class="bg-slate-900/50 rounded-lg p-3 border border-white/5 group-hover:bg-slate-900/70 transition-colors">
                                <span class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Incidents</span>
                                <span class="text-xl font-mono text-white">{{ $store->incidents_count }}</span>
                            </div>
                        </div>

                        <!-- Footer / Actions -->
                        <div class="relative flex items-center justify-between pt-4 border-t border-white/5">
                            <span class="text-xs text-slate-500 truncate max-w-[150px]" title="{{ $store->domain }}">
                                {{ $store->domain ?? 'No Domain' }}
                            </span>
                            <a href="{{ route('stores.show', $store) }}" class="flex items-center gap-2 text-sm font-bold text-white bg-white/5 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-lg transition-all duration-300 group-hover:translate-x-1">
                                Manage
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="flex flex-col items-center justify-center py-24 bg-[#1e293b]/50 rounded-2xl border border-dashed border-white/10 hover:border-white/20 transition-colors">
                            <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center mb-6 border border-emerald-500/20">
                                <svg class="w-10 h-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Start your first project</h3>
                            <p class="text-slate-400 text-sm mb-8 max-w-sm text-center">
                                Monitor your application performance, track incidents, and optimize for revenue.
                            </p>
                            <a href="{{ route('stores.create') }}" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-105">
                                + Create New Project
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>