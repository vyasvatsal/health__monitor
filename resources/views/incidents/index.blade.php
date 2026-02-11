<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Incidents') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-6">

            <!-- Incident Stats (Optional - can be expanded later) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#1e293b] p-6 rounded-lg border border-white/10 shadow-sm">
                    <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Open Incidents</div>
                    <div class="text-3xl font-bold text-white">
                        {{ $incidents->where('status', 'open')->count() }}
                    </div>
                </div>
                <div class="bg-[#1e293b] p-6 rounded-lg border border-white/10 shadow-sm">
                    <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Investigating</div>
                    <div class="text-3xl font-bold text-white">
                        {{ $incidents->where('status', 'investigating')->count() }}
                    </div>
                </div>
                <div class="bg-[#1e293b] p-6 rounded-lg border border-white/10 shadow-sm">
                    <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-1">Resolved (Last 30
                        Days)</div>
                    <div class="text-3xl font-bold text-white">
                        {{ $incidents->where('status', 'resolved')->count() }}
                    </div>
                </div>
            </div>

            <!-- Incidents List -->
            <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 flex flex-col flex-1">
                @if($incidents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="bg-slate-900/50 text-slate-200 uppercase font-medium border-b border-white/5">
                                <tr>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Severity</th>
                                    <th class="px-6 py-4 w-full">Incident</th>
                                    <th class="px-6 py-4 text-right">Time</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($incidents as $incident)
                                                <tr class="hover:bg-white/5 transition-colors group">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php
                                                            $statusClasses = match ($incident->status) {
                                                                'open' => 'bg-red-500/10 text-red-500 border border-red-500/20',
                                                                'investigating' => 'bg-amber-500/10 text-amber-500 border border-amber-500/20',
                                                                'resolved' => 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20',
                                                                default => 'bg-slate-500/10 text-slate-500 border border-slate-500/20'
                                                            };
                                                        @endphp
                                    <span
                                                            class="px-2.5 py-1 rounded-full text-xs font-medium uppercase tracking-wide {{ $statusClasses }}">
                                                            {{ $incident->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php
                                                            $severityColor = match ($incident->severity) {
                                                                'critical' => 'text-red-400',
                                                                'warning' => 'text-amber-400',
                                                                'info' => 'text-blue-400',
                                                                default => 'text-slate-400'
                                                            };
                                                            $severityIcon = match ($incident->severity) {
                                                                'critical' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                                                                'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                                                                default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                                                            };
                                                        @endphp
                                                        <div class="flex items-center gap-2 {{ $severityColor }}">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                {!! $severityIcon !!}
                                                            </svg>
                                                            <span class="font-medium capitalize">{{ $incident->severity }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="flex flex-col">
                                                            <span
                                                                class="text-white font-medium text-base mb-1">{{ $incident->title }}</span>
                                                            <span class="text-slate-500 text-xs">
                                                                {{ $incident->store->name ?? 'Unknown Store' }} &bull;
                                                                {{ Str::limit($incident->description, 80) }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                                        <div class="flex flex-col gap-1">
                                                            <span class="text-slate-300">{{ $incident->created_at->diffForHumans() }}</span>
                                                            <span
                                                                class="text-slate-600">{{ $incident->created_at->format('M d, H:i') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <a href="{{ route('incidents.show', $incident) }}"
                                                            class="text-purple-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/5 inline-flex">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                        <div
                            class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center mb-6 ring-1 ring-emerald-500/20">
                            <svg class="w-10 h-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">System Healthy</h3>
                        <p class="text-slate-400 max-w-sm mx-auto">No open incidents found. Your stores are running
                            smoothly.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>