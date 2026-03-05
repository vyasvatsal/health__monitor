<x-app-layout>
    <x-slot name="header">
        {{ __('Incident Details') }}
    </x-slot>

    <div class="py-6 flex-1 flex flex-col">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-6">

            <!-- Breadcrumb & Back -->
            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                <a href="{{ route('incidents.index') }}" class="hover:text-indigo-400 transition-colors">Issues</a>
                <span>/</span>
                <span class="text-slate-300">Issue Details</span>
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

            <!-- Sentry Issue Title & Header Area -->
            <div class="mb-2 w-full flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-[10px] font-black tracking-widest px-2 py-0.5 rounded text-white bg-slate-800 border-b border-white/10 uppercase drop-shadow-sm">ERROR</span>
                        <h2 class="font-black text-2xl text-white tracking-tight">{{ $incident->title }}</h2>
                    </div>
                    <div class="flex items-center flex-wrap gap-x-4 gap-y-2 text-xs font-mono mt-3">
                        @if($incident->status === 'open' || $incident->status === 'investigating')
                            <span class="text-red-500 font-bold tracking-widest uppercase flex items-center gap-1.5">
                                Unhandled
                            </span>
                        @endif
                        <span class="text-yellow-500 font-bold border border-yellow-500/30 bg-yellow-500/10 px-1.5 py-0.5 rounded uppercase tracking-widest">{{ $incident->severity }}</span>
                        <span class="text-slate-400 truncate max-w-2xl bg-slate-900/50 px-2 py-1 rounded">{{ Str::limit($incident->description, 120) }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('incidents.edit', $incident) }}"
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-5 rounded-md border border-indigo-500 transition-all text-sm shadow-[0_0_15px_rgba(79,70,229,0.2)]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>

            <!-- Sentry Mock Graph Strip -->
            <div class="bg-[#1e1e24] border border-white/5 rounded-lg p-4 mb-2 relative overflow-hidden flex items-end justify-between h-28 gap-[3px] group">
                <!-- Mock graph bars -->
                @foreach(range(1, 80) as $i)
                    @php 
                        $isHigh = in_array($i, [20, 21, 55, 56, 78, 79, 80]);
                        $h = $isHigh ? rand(60, 90) : rand(5, 25); 
                        $isRecent = $i > 75; 
                    @endphp
                    <div class="w-full bg-indigo-500/{{ $isRecent ? '80' : '40' }} rounded-t-sm hover:bg-indigo-400 transition-all group-hover:bg-indigo-500/{{ $isRecent ? '90' : '50' }}" style="height: {{ $h }}%"></div>
                @endforeach
                <div class="absolute top-3 right-5 text-sm font-semibold text-slate-400 flex items-center gap-4 bg-slate-900/80 px-4 py-2 rounded-full border border-white/5 backdrop-blur-sm">
                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> Events 389</div> 
                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Users 0</div>
                </div>
                
                <div class="absolute inset-x-0 bottom-0 top-auto h-px bg-white/5"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Content (Left Pane) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Highlights -->
                    <div class="bg-[#1e1e24] rounded-lg border border-white/5 overflow-hidden shadow-lg">
                        <div class="px-5 py-3.5 border-b border-white/5 bg-[#25252c] flex justify-between items-center">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Highlights
                            </h3>
                        </div>
                        <div class="p-0">
                            <table class="w-full text-sm text-left">
                                <tbody class="bg-transparent text-slate-300 font-mono text-[13px] divide-y divide-white/5">
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-5 py-3 text-slate-500 w-40 border-r border-white/5 font-semibold">handled</td>
                                        <td class="px-5 py-3 text-red-400 font-semibold">no</td>
                                    </tr>
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-5 py-3 text-slate-500 border-r border-white/5 font-semibold">level</td>
                                        <td class="px-5 py-3 text-yellow-400 font-semibold">{{ $incident->severity }}</td>
                                    </tr>
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-5 py-3 text-slate-500 border-r border-white/5 font-semibold">transaction</td>
                                        <td class="px-5 py-3 text-indigo-400 truncate max-w-md"><a href="{{ $incident->store->url }}" target="_blank" class="hover:underline">{{ $incident->store->url }}</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Description / Trace -->
                    <div class="bg-[#1e1e24] rounded-lg border border-white/5 overflow-hidden shadow-lg">
                        <div class="px-5 py-3.5 border-b border-white/5 bg-[#25252c] flex justify-between items-center">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                Stack Trace / Details
                            </h3>
                            <button class="text-xs text-slate-400 hover:text-white border border-slate-700 bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded transition-colors font-medium">Copy JSON</button>
                        </div>
                        <div class="p-5 overflow-x-auto bg-[#17171a]">
                            <pre class="text-[13px] text-slate-300 font-mono whitespace-pre-wrap leading-loose">{{ $incident->description }}</pre>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right Pane) -->
                <div class="space-y-6">
                    
                    <!-- Times metadata -->
                    <div class="bg-[#1e1e24] rounded-lg border border-white/5 overflow-hidden border-t-2 border-t-indigo-500 shadow-lg">
                        <div class="p-5 grid grid-cols-2 gap-4 text-sm bg-gradient-to-b from-[#25252c] to-transparent">
                            <div>
                                <p class="text-slate-500 mb-1 font-medium text-xs uppercase tracking-wider">Last seen</p>
                                <p class="text-white font-medium">{{ $incident->updated_at ? $incident->updated_at->diffForHumans() : $incident->created_at->diffForHumans() }}</p>
                                <p class="text-[10px] text-slate-600 mt-1">{{ $incident->updated_at ? $incident->updated_at->format('M j, Y g:i A') : $incident->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 mb-1 font-medium text-xs uppercase tracking-wider">First seen</p>
                                <p class="text-white font-medium">{{ $incident->created_at->diffForHumans() }}</p>
                                <p class="text-[10px] text-slate-600 mt-1">{{ $incident->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- AI Seer Button -->
                    <div class="bg-gradient-to-b from-[#2a1a4a] to-[#1e1e24] rounded-lg border border-purple-500/30 overflow-hidden relative shadow-[0_0_30px_rgba(168,85,247,0.15)] group">
                        
                        <!-- Sparkles decoration -->
                        <div class="absolute top-0 right-0 p-4 opacity-30 group-hover:opacity-100 transition-opacity">
                            <svg class="w-8 h-8 text-purple-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        
                        <div class="p-6 relative z-10">
                            <h4 class="text-sm font-bold text-purple-300 flex items-center gap-2 mb-4 tracking-wide">
                                Seer AI
                            </h4>
                            
                            <!-- Issue Tracking AI Prompt Box -->
                            <div class="text-[13px] text-purple-100/90 mb-5 bg-black/40 p-4 rounded-md border border-purple-500/20 shadow-inner">
                                <p class="text-[10px] font-black tracking-widest text-purple-400/80 mb-2 uppercase">Initial Guess</p>
                                <p class="leading-relaxed">The server may be returning an unexpected response format causing the parser to fail. The exception indicates a malformed payload.</p>
                            </div>
                            
                            <button class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 px-4 rounded-md transition-all flex items-center justify-between gap-2 shadow-[0_0_15px_rgba(168,85,247,0.4)] group-hover:shadow-[0_0_20px_rgba(168,85,247,0.6)]">
                                <span>Find Root Cause</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Properties Module -->
                    <div class="bg-[#1e1e24] rounded-lg border border-white/5 overflow-hidden shadow-lg">
                        <div class="px-5 py-3.5 border-b border-white/5 bg-[#25252c]">
                            <h4 class="text-sm font-bold text-white">Properties</h4>
                        </div>
                        <div class="p-5 space-y-4 bg-slate-900/40 text-[13px]">
                            <div>
                                <p class="text-xs text-slate-500 mb-1.5 font-medium uppercase tracking-wider">Status</p>
                                @php
                                    $statusColors = [
                                        'open' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                                        'investigating' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                                        'identified' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                        'monitoring' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                        'resolved' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]',
                                    ];
                                    $scolor = $statusColors[$incident->status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                @endphp
                                <div class="inline-flex {{ $scolor }} px-2.5 py-1 rounded text-[11px] font-bold uppercase tracking-wide items-center gap-1.5 shadow-sm">
                                    @if($incident->status !== 'resolved')
                                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                    @endif
                                    {{ str_replace('_', ' ', $incident->status) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity timeline -->
                    <div class="bg-[#1e1e24] rounded-lg border border-white/5 overflow-hidden shadow-lg">
                        <div class="px-5 py-3.5 border-b border-white/5 bg-[#25252c]">
                            <h4 class="text-sm font-bold text-white">Activity</h4>
                        </div>
                        <div class="p-5 bg-slate-900/40">
                            <div class="relative pl-5 space-y-5 before:absolute before:inset-y-0 before:left-[11px] before:w-[1px] before:bg-slate-700/50">
                                
                                <div class="relative">
                                    <div class="absolute -left-[24px] bg-slate-800 w-3 h-3 rounded-full border-2 border-slate-500 mt-1 shadow-sm"></div>
                                    <div class="text-[13px]">
                                        <p class="text-slate-300 font-medium">First Seen</p>
                                        <p class="text-[11px] font-mono text-slate-500 mt-1 bg-slate-900/50 inline-block px-1.5 py-0.5 rounded">{{ $incident->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                                
                                @if($incident->resolved_at)
                                <div class="relative">
                                    <div class="absolute -left-[24px] bg-[#1e1e24] w-3 h-3 rounded-full border-2 border-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)] mt-1"></div>
                                    <div class="text-[13px]">
                                        <p class="text-emerald-400 font-medium">Resolved by System</p>
                                        <p class="text-[11px] font-mono text-slate-500 mt-1 bg-slate-900/50 inline-block px-1.5 py-0.5 rounded">{{ $incident->resolved_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>