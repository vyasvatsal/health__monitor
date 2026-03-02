<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2
                class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400 leading-tight">
                {{ __('Incidents Overview') }}
            </h2>
            <a href="{{ route('incidents.create') }}"
                class="flex items-center gap-2 text-sm text-white bg-gradient-to-r from-red-600 to-orange-600 px-5 py-2.5 rounded-xl font-bold shadow-[0_0_20px_rgba(239,68,68,0.3)] hover:shadow-[0_0_25px_rgba(239,68,68,0.5)] transition-all hover:scale-[1.02] border border-red-500/50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Report Incident
            </a>
        </div>
    </x-slot>

    <div class="py-8 flex-1 flex flex-col">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-8">

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

            <div
                class="bg-slate-900/40 backdrop-blur-xl overflow-hidden rounded-2xl border border-white/5 shadow-2xl relative group transition-all duration-300 hover:border-red-500/20">
                <div
                    class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/30 to-transparent opacity-50">
                </div>

                <div class="p-0">
                    @if($incidents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-slate-400">
                                <thead class="text-xs text-slate-300 uppercase bg-slate-900/80 border-b border-white/5">
                                    <tr>
                                        <th scope="col" class="px-6 py-5 font-bold tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-5 font-bold tracking-wider">Severity</th>
                                        <th scope="col" class="px-6 py-5 font-bold tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-5 font-bold tracking-wider">Reported At</th>
                                        <th scope="col" class="px-6 py-5 text-right font-bold tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidents as $incident)
                                        <tr
                                            class="bg-transparent border-b border-white/5 hover:bg-slate-800/30 transition-colors group/row">
                                            <th scope="row" class="px-6 py-5 font-medium text-white whitespace-nowrap">
                                                <a href="{{ route('incidents.show', $incident) }}"
                                                    class="hover:text-red-400 transition-colors flex items-center gap-2">
                                                    {{ $incident->title }}
                                                </a>
                                            </th>
                                            <td class="px-6 py-5">
                                                @php
                                                    $severityColors = [
                                                        'critical' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                                                        'major' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
                                                        'minor' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                                        'maintenance' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                                    ];
                                                    $color = $severityColors[$incident->severity] ?? 'bg-slate-500/10 text-slate-400 border border-slate-500/20';
                                                @endphp
                                                <span
                                                    class="{{ $color }} text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                                    {{ $incident->severity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5">
                                                @php
                                                    $statusColors = [
                                                        'open' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                                                        'investigating' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                                                        'identified' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                                        'monitoring' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                                        'resolved' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                                    ];
                                                    $statusColor = $statusColors[$incident->status] ?? 'bg-slate-500/10 text-slate-400 border border-slate-500/20';
                                                @endphp
                                                <span
                                                    class="{{ $statusColor }} text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider relative flex items-center w-max gap-1.5">
                                                    @if($incident->status !== 'resolved')
                                                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                                    @endif
                                                    {{ str_replace('_', ' ', $incident->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span class="text-slate-400">
                                                    {{ $incident->created_at->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-5 text-right space-x-2 opacity-100 sm:opacity-0 sm:group-hover/row:opacity-100 transition-opacity">
                                                <a href="{{ route('incidents.edit', $incident) }}"
                                                    class="inline-block p-2 bg-slate-800/80 hover:bg-blue-600 rounded-lg text-slate-400 hover:text-white transition-all border border-white/5 hover:border-blue-500 hover:shadow-[0_0_15px_rgba(37,99,235,0.4)]"
                                                    title="Edit Incident">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('incidents.destroy', $incident) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this incident?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 bg-slate-800/80 hover:bg-red-600 rounded-lg text-slate-400 hover:text-white transition-all border border-white/5 hover:border-red-500 hover:shadow-[0_0_15px_rgba(239,68,68,0.4)]"
                                                        title="Delete Incident">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-20 px-6">
                            <div
                                class="w-24 h-24 rounded-2xl bg-gradient-to-br from-emerald-500/10 to-transparent flex items-center justify-center mx-auto mb-6 border border-emerald-500/20 relative">
                                <div class="absolute inset-0 rounded-2xl bg-emerald-500/10 animate-ping opacity-20"></div>
                                <svg class="w-12 h-12 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">Systems Operational</h3>
                            <p class="text-slate-400 text-lg max-w-md mx-auto mb-8">Everything is running smoothly! No
                                ongoing incidents. Report an issue above if something comes up.</p>

                            <a href="{{ route('incidents.create') }}"
                                class="inline-flex items-center gap-2 text-white bg-slate-800 hover:bg-slate-700 font-bold py-3 px-6 rounded-xl border border-white/5 transition-all">
                                Report Issue
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>