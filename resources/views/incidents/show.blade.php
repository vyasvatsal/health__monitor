<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('incidents.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Incident Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-6">

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Details Column (Left - 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Incident Header Card -->
                    <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    @php
                                        $severityColor = match ($incident->severity) {
                                            'critical' => 'text-red-400 bg-red-400/10 border-red-400/20',
                                            'warning' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                            'info' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                                            default => 'text-slate-400 bg-slate-400/10 border-slate-400/20'
                                        };
                                        $statusColor = match ($incident->status) {
                                            'open' => 'text-red-400 bg-red-400/10 border-red-400/20',
                                            'investigating' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                            'resolved' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                                            default => 'text-slate-400 bg-slate-400/10 border-slate-400/20'
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wide {{ $severityColor }}">
                                        {{ $incident->severity }}
                                    </span>
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wide {{ $statusColor }}">
                                        {{ $incident->status }}
                                    </span>
                                </div>
                                <h1 class="text-2xl font-bold text-white mb-2">{{ $incident->title }}</h1>
                                <div class="flex items-center text-sm text-slate-400">
                                    <span>Affecting</span>
                                    <a href="{{ route('stores.show', $incident->store) }}"
                                        class="ml-1 text-purple-400 hover:text-purple-300 font-medium transition-colors">
                                        {{ $incident->store->name }}
                                    </a>
                                    <span class="mx-2">&bull;</span>
                                    <span>reported {{ $incident->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="prose prose-invert max-w-none">
                            <h3 class="text-slate-200 font-semibold mb-2">Description</h3>
                            <p class="text-slate-300 leading-relaxed">
                                {{ $incident->description ?? 'No description provided.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Timeline / Updates (Placeholder for future) -->
                    {{-- Future: Add timeline features here --}}
                </div>

                <!-- Actions Column (Right - 1/3) -->
                <div class="space-y-6">
                    <!-- Status Management -->
                    <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Update Status</h3>

                        <form method="POST" action="{{ route('incidents.update', $incident) }}">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Current Status</label>
                                    <select name="status"
                                        class="w-full bg-[#0f172a] border border-white/10 rounded-lg text-white text-sm focus:ring-purple-500 focus:border-purple-500 py-2.5">
                                        <option value="open" {{ $incident->status === 'open' ? 'selected' : '' }}>Open
                                        </option>
                                        <option value="investigating" {{ $incident->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                        <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>
                                            Resolved</option>
                                    </select>
                                </div>

                                <button type="submit"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 rounded-lg transition-colors flex items-center justify-center">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Meta Info -->
                    <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-6">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Metadata</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Incident ID</dt>
                                <dd class="text-white font-mono">#{{ $incident->id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Created</dt>
                                <dd class="text-white">{{ $incident->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            @if($incident->resolved_at)
                                <div class="flex justify-between">
                                    <dt class="text-emerald-500">Resolved</dt>
                                    <dd class="text-white">{{ $incident->resolved_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div class="pt-3 mt-3 border-t border-white/5">
                                    <dt class="text-slate-500 mb-1">Duration</dt>
                                    <dd class="text-emerald-400 font-medium">
                                        {{ $incident->created_at->diffForHumans($incident->resolved_at, true) }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>