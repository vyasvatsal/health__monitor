@extends('layouts.app')

@section('content')
    <div class="py-6 flex-1 flex flex-col h-full overflow-hidden">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col h-full">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <!-- Project Switcher -->
                    <div class="relative group">
                        <button
                            class="flex items-center gap-2 text-2xl font-bold text-white hover:text-slate-200 transition-colors">
                            {{ $currentStore->name ?? 'Select Project' }}
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Dropdown -->
                        <div
                            class="absolute left-0 mt-2 w-56 bg-[#1e293b] border border-slate-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                            <div class="py-1">
                                @foreach($allStores as $s)
                                    <a href="{{ route('monitor.errors', ['store_id' => $s->id]) }}"
                                        class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white flex justify-between items-center">
                                        {{ $s->name }}
                                        @if($s->id === $currentStore->id)
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button onclick="document.getElementById('integration-modal').showModal()"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                        <i class="material-icons text-sm">add_link</i> Connection Guide
                    </button>
                    <!-- Time Range Filter (Mock) -->
                    <div class="bg-[#1e293b] rounded-lg border border-slate-700 p-1 flex">
                        <button class="px-3 py-1 text-xs font-medium text-white bg-slate-700 rounded shadow-sm">24h</button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Errors -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-blue-500/30 transition-all duration-300 group">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">Total Errors
                                </div>
                                <div
                                    class="text-2xl font-bold text-white tracking-tight group-hover:text-blue-400 transition-colors">
                                    {{ number_format($stats['total_24h']) }}
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                                <i class="material-icons text-xl">bug_report</i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Critical Issues -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-rose-500/30 transition-all duration-300 group">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">Critical</div>
                                <div
                                    class="text-2xl font-bold text-white tracking-tight group-hover:text-rose-400 transition-colors">
                                    {{ number_format($stats['critical']) }}
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                                <i class="material-icons text-xl">warning</i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resolved -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-emerald-500/30 transition-all duration-300 group">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">Resolved</div>
                                <div
                                    class="text-2xl font-bold text-white tracking-tight group-hover:text-emerald-400 transition-colors">
                                    {{ number_format($stats['resolved']) }}
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                                <i class="material-icons text-xl">check_circle</i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impacted Users -->
                <div
                    class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 hover:border-purple-500/30 transition-all duration-300 group">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-slate-400 text-xs font-medium uppercase tracking-wider mb-1">Impacted Users
                                </div>
                                <div
                                    class="text-2xl font-bold text-white tracking-tight group-hover:text-purple-400 transition-colors">
                                    {{ number_format($stats['impacted_users']) }}
                                </div>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                                <i class="material-icons text-xl">group</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area (Split View) -->
            <div class="flex-1 bg-[#1e293b] rounded-xl border border-white/10 overflow-hidden flex flex-col"
                x-data="errorMonitor(@json($errors))">

                <!-- Toolbar -->
                <div
                    class="p-4 border-b border-white/5 flex flex-col sm:flex-row justify-between items-center gap-4 bg-[#0f172a]/50">
                    <div class="flex items-center space-x-1 bg-[#0f172a] p-1 rounded-lg border border-white/5">
                        <button @click="tab = 'all'"
                            :class="tab === 'all' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all">All</button>
                        <button @click="tab = 'new'"
                            :class="tab === 'new' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/20 shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            New
                        </button>
                        <button @click="tab = 'investigating'"
                            :class="tab === 'investigating' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/20 shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all">Investigating</button>
                        <button @click="tab = 'resolved'"
                            :class="tab === 'resolved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all">Resolved</button>
                    </div>

                    <div class="relative w-full sm:w-72">
                        <i class="material-icons absolute left-3 top-2.5 text-slate-500 text-lg">search</i>
                        <input x-model="search" type="text" placeholder="Search errors, files..."
                            class="w-full bg-[#0f172a] border border-slate-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <div class="flex-1 overflow-hidden relative flex flex-col">
                    <div
                        class="bg-[#0f172a] border-b border-white/5 flex text-xs font-bold text-slate-400 uppercase tracking-wider sticky top-0 z-10">
                        <div class="px-6 py-3 w-32">Type</div>
                        <div class="px-6 py-3 flex-1">Message</div>
                        <div class="px-6 py-3 w-48">Location</div>
                        <div class="px-6 py-3 w-24 text-center">Count</div>
                        <div class="px-6 py-3 w-24 text-center">Users</div>
                        <div class="px-6 py-3 w-32 text-right">Last Seen</div>
                        <div class="px-6 py-3 w-24 text-center">Status</div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse table-fixed">
                            <tbody class="divide-y divide-white/5">
                                <template x-for="error in filteredErrors" :key="error.id">
                                    <tr @click="selectedError = error"
                                        class="hover:bg-white/[0.02] cursor-pointer transition-colors group border-b border-white/5 last:border-0"
                                        :class="{'bg-white/[0.04]': selectedError && selectedError.id === error.id}">

                                        <td class="px-6 py-4 align-top w-32">
                                            <span x-bind:class="{
                                                                                        'bg-rose-500/10 text-rose-400 border-rose-500/20': error.severity === 'critical' || error.type === 'Error',
                                                                                        'bg-amber-500/10 text-amber-400 border-amber-500/20': error.severity === 'warning' || error.type === 'ResourceError',
                                                                                        'bg-purple-500/10 text-purple-400 border-purple-500/20': error.type === 'NetworkError',
                                                                                        'bg-blue-500/10 text-blue-400 border-blue-500/20': (error.severity === 'info' || !error.severity) && error.type !== 'NetworkError' && error.type !== 'ResourceError' && error.type !== 'Error'
                                                                                    }"
                                                class="px-2.5 py-1 rounded-md text-[11px] font-bold border uppercase tracking-wider block w-fit text-center shadow-sm"
                                                x-text="error.type || 'ERROR'"></span>
                                        </td>

                                        <td class="px-6 py-4 align-top">
                                            <div class="flex flex-col gap-1.5">
                                                <!-- Main Title / Message -->
                                                <span
                                                    class="text-sm font-semibold text-slate-200 group-hover:text-white transition-colors font-sans leading-snug break-words"
                                                    x-text="error.message"></span>

                                                <!-- Secondary Info (Raw Message Preview if different) -->
                                                <template x-if="error.raw_message && error.message !== error.raw_message">
                                                    <span
                                                        class="text-xs text-slate-500 font-mono truncate max-w-lg opacity-80"
                                                        x-text="error.raw_message"></span>
                                                </template>

                                                <!-- AI Badge if available -->
                                                <template x-if="error.ai_analysis">
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span
                                                            class="flex items-center gap-1 text-[10px] font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 shadow-sm">
                                                            <i class="material-icons text-[10px]">auto_awesome</i> AI
                                                            Analyzed
                                                        </span>
                                                        <span class="text-xs text-slate-500 truncate max-w-md"
                                                            x-text="error.ai_analysis.summary"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>

                                        <!-- Location -->
                                        <td class="px-6 py-4 align-top w-48">
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="text-xs text-slate-400 font-mono truncate hover:text-slate-300 transition-colors"
                                                    x-text="error.file" :title="error.file"></span>
                                                <span
                                                    class="text-[11px] text-emerald-500 font-mono flex items-center gap-1">
                                                    <span class="opacity-50">Line:</span>
                                                    <span x-text="error.line" class="font-bold"></span>
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 align-top w-24 text-center">
                                            <div
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md bg-slate-800 border border-slate-700">
                                                <span class="text-sm font-bold text-slate-300"
                                                    x-text="error.occurrences"></span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 align-top w-24 text-center">
                                            <div
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md bg-slate-800 border border-slate-700">
                                                <span class="text-sm font-bold text-slate-300"
                                                    x-text="error.users_impacted"></span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 align-top w-32 text-right">
                                            <span class="text-xs text-slate-500 font-medium whitespace-nowrap"
                                                x-text="error.timestamp"></span>
                                        </td>

                                        <td class="px-6 py-4 align-top w-24 text-center">
                                            <div class="flex flex-col gap-2 items-center">
                                                <span x-show="error.status == 'new'"
                                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-rose-400 bg-rose-500/10 rounded-md border border-rose-500/10 w-full">
                                                    New
                                                </span>
                                                <span x-show="error.status == 'investigating'"
                                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-blue-400 bg-blue-500/10 rounded-md border border-blue-500/10 w-full">
                                                    Investigating
                                                </span>
                                                <span x-show="error.status == 'resolved'"
                                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-emerald-400 bg-emerald-500/10 rounded-md border border-emerald-500/10 w-full">
                                                    Resolved
                                                </span>

                                                <!-- Explicit Action -->
                                                <button
                                                    class="text-[10px] text-slate-500 hover:text-white uppercase font-bold tracking-wider flex items-center gap-1 transition-colors group-hover:opacity-100 opacity-50">
                                                    View Details <i class="material-icons text-[10px]">arrow_forward</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <!-- Empty State -->
                                <tr x-show="filteredErrors.length === 0">
                                    <td colspan="7" class="p-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mb-4">
                                                <i class="material-icons text-3xl opacity-50">search_off</i>
                                            </div>
                                            <h3 class="text-white font-medium mb-1">No errors found</h3>
                                            <p class="text-sm">Try adjusting your search or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Error Details Slide-over/Modal -->
                <div x-show="selectedError" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                    class="fixed inset-y-0 right-0 w-full sm:w-[600px] bg-[#0f172a] shadow-2xl border-l border-white/10 z-50 flex flex-col"
                    style="display: none;">

                    <!-- Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-white/10 bg-[#1e293b]/50 backdrop-blur-md">
                        <div>
                            <span x-bind:class="{
                                                                                                                'bg-rose-500/10 text-rose-400 border-rose-500/20': selectedError?.severity === 'critical',
                                                                                                                'bg-amber-500/10 text-amber-400 border-amber-500/20': selectedError?.severity === 'warning',
                                                                                                                'bg-blue-500/10 text-blue-400 border-blue-500/20': selectedError?.severity === 'info'
                                                                                                            }"
                                class="px-2 py-1 rounded text-xs font-bold border uppercase tracking-wider"
                                x-text="selectedError?.type"></span>
                            <h2 class="text-white font-bold text-lg mt-2 truncate max-w-md">Error Details</h2>
                        </div>
                        <button @click="selectedError = null"
                            class="text-slate-400 hover:text-white transition-colors p-2 hover:bg-white/5 rounded-lg">
                            <i class="material-icons">close</i>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">

                        <!-- AI Analysis -->
                        <template x-if="selectedError?.ai_analysis">
                            <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-lg p-5 mb-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="p-1.5 bg-indigo-500/20 rounded-lg">
                                        <i class="material-icons text-indigo-400 text-sm">auto_awesome</i>
                                    </div>
                                    <div>
                                        <h3 class="text-indigo-400 text-sm font-bold uppercase tracking-wider">AI Diagnostic
                                            Report</h3>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs text-slate-400">Powered by Llama 3.3</span>
                                        </div>
                                    </div>
                                    <div class="ml-auto flex items-center gap-2">
                                        <span class="text-xs text-slate-400 uppercase font-bold">Severity Score</span>
                                        <span
                                            class="px-2 py-1 rounded bg-slate-800 border border-white/10 text-white font-mono text-xs font-bold"
                                            x-text="selectedError.ai_analysis.severity_score + '/10'"></span>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Headline -->
                                    <div>
                                        <h4 class="text-xl font-bold text-white mb-2"
                                            x-text="selectedError.ai_analysis.title">
                                        </h4>
                                        <p class="text-slate-300 text-sm leading-relaxed"
                                            x-text="selectedError.ai_analysis.summary"></p>
                                    </div>

                                    <!-- Root Cause -->
                                    <div class="bg-black/20 rounded-lg p-4 border border-white/5">
                                        <h5
                                            class="text-indigo-300 text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-2">
                                            <i class="material-icons text-xs">search</i> Root Cause
                                        </h5>
                                        <p class="text-slate-300 text-sm" x-text="selectedError.ai_analysis.root_cause"></p>
                                    </div>

                                    <!-- Solution -->
                                    <div>
                                        <h5
                                            class="text-emerald-400 text-xs font-bold uppercase tracking-wider mb-3 flex items-center gap-2">
                                            <i class="material-icons text-xs">build</i> Recommended Fix
                                        </h5>

                                        <!-- Code Fix -->
                                        <template x-if="selectedError.ai_analysis.code_fix">
                                            <div
                                                class="bg-[#0f172a] rounded-lg p-4 border border-white/10 mb-4 overflow-x-auto group relative">
                                                <div
                                                    class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button class="p-1 text-slate-400 hover:text-white"
                                                        onclick="navigator.clipboard.writeText(this.parentElement.nextElementSibling.innerText)">
                                                        <i class="material-icons text-sm">content_copy</i>
                                                    </button>
                                                </div>
                                                <code
                                                    class="text-emerald-300 text-xs font-mono break-all whitespace-pre-wrap"
                                                    x-text="selectedError.ai_analysis.code_fix"></code>
                                            </div>
                                        </template>

                                        <!-- Steps -->
                                        <ul class="space-y-2">
                                            <template x-for="(step, index) in selectedError.ai_analysis.solution_steps"
                                                :key="index">
                                                <li class="flex gap-3 text-sm text-slate-300">
                                                    <span
                                                        class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs font-bold border border-emerald-500/20"
                                                        x-text="index + 1"></span>
                                                    <span x-text="step"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    <!-- Prevention -->
                                    <div
                                        class="flex gap-3 items-start bg-blue-500/5 p-3 rounded-lg border border-blue-500/10">
                                        <i class="material-icons text-blue-400 text-sm mt-0.5">lightbulb</i>
                                        <div>
                                            <span class="text-blue-400 text-xs font-bold uppercase block mb-1">Prevention
                                                Tip</span>
                                            <p class="text-slate-300 text-xs" x-text="selectedError.ai_analysis.prevention">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Message -->
                        <div class="bg-rose-500/5 border border-rose-500/10 rounded-xl p-5">
                            <h3
                                class="text-rose-400 text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="material-icons text-sm">error_outline</i> Error Message
                            </h3>
                            <p class="text-white font-mono text-sm break-words leading-relaxed select-text"
                                x-text="selectedError?.message"></p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Location -->
                            <div class="bg-[#1e293b] rounded-xl border border-white/5 p-5">
                                <h3
                                    class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <i class="material-icons text-sm">place</i> Location
                                </h3>
                                <div class="flex flex-col gap-3">
                                    <div>
                                        <span
                                            class="text-slate-500 block text-[10px] uppercase font-semibold mb-1">File</span>
                                        <div class="flex items-center justify-between group">
                                            <span class="text-slate-200 font-mono text-xs break-all leading-relaxed"
                                                x-text="selectedError?.file"></span>
                                            <button
                                                class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-white transition-opacity p-1"
                                                @click="navigator.clipboard.writeText(selectedError?.file)">
                                                <i class="material-icons text-sm">content_copy</i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-white/5 pt-3">
                                        <span class="text-slate-500 text-xs">Line Number</span>
                                        <span
                                            class="text-emerald-400 font-mono font-bold bg-emerald-500/10 px-2 py-0.5 rounded text-xs"
                                            x-text="selectedError?.line"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Environment -->
                            <div class="bg-[#1e293b] rounded-xl border border-white/5 p-5">
                                <h3
                                    class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <i class="material-icons text-sm">computer</i> Environment
                                </h3>
                                <div class="flex flex-col gap-3">
                                    <div>
                                        <span class="text-slate-500 block text-[10px] uppercase font-semibold mb-1">Browser
                                            /
                                            Client</span>
                                        <span class="text-slate-200 text-xs break-words block leading-relaxed"
                                            x-text="selectedError?.browser"></span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-white/5 pt-3">
                                        <span class="text-slate-500 text-xs">Last Seen</span>
                                        <div class="flex items-center gap-1.5 text-slate-300">
                                            <i class="material-icons text-xs text-slate-500">schedule</i>
                                            <span class="text-xs font-medium" x-text="selectedError?.timestamp"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stack Trace -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3
                                    class="text-slate-400 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                                    <i class="material-icons text-sm">code</i> Stack Trace
                                </h3>
                                <button
                                    class="text-xs text-slate-500 hover:text-white flex items-center gap-1 transition-colors"
                                    @click="navigator.clipboard.writeText(selectedError?.trace)">
                                    <i class="material-icons text-xs">content_copy</i> Copy Trace
                                </button>
                            </div>
                            <div class="bg-[#0b1120] rounded-xl border border-white/10 p-0 overflow-hidden group">
                                <div class="max-h-[300px] overflow-y-auto custom-scrollbar p-4">
                                    <pre class="text-[11px] text-blue-300/90 font-mono whitespace-pre-wrap leading-loose select-text"
                                        x-text="selectedError?.trace"></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="p-6 border-t border-white/10 bg-[#1e293b]/50 backdrop-blur-md flex gap-3">
                        <button
                            class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                            <i class="material-icons text-sm">check</i> Mark as Resolved
                        </button>
                        <button
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg border border-slate-600 transition-colors flex items-center gap-2">
                            <i class="material-icons text-sm">share</i> Share
                        </button>
                    </div>
                </div>

                <!-- Backdrop for Mobile -->
                <div x-show="selectedError" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
                    x-transition.opacity @click="selectedError = null"></div>

                <!-- Integration Modal -->
                <dialog id="integration-modal"
                    class="bg-transparent backdrop:bg-black/80 backdrop:backdrop-blur-sm p-0 w-full max-w-2xl rounded-2xl shadow-2xl open:animate-fade-in">
                    <div class="bg-[#0f172a] border border-white/10 rounded-2xl flex flex-col text-white">
                        <div class="flex items-center justify-between p-6 border-b border-white/5">
                            <h3 class="text-lg font-bold">Connect Your Project</h3>
                            <button onclick="document.getElementById('integration-modal').close()"
                                class="text-slate-400 hover:text-white transition-colors">
                                <i class="material-icons">close</i>
                            </button>
                        </div>
                        <div class="p-6 space-y-6" x-data="{ tab: 'laravel' }">
                            <p class="text-sm text-slate-400">Use the SDK to send errors from your application to Health
                                Monitor.</p>

                            <div class="flex border-b border-white/10">
                                <button @click="tab = 'laravel'"
                                    :class="tab === 'laravel' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">Laravel
                                    (Backend)</button>
                                <button @click="tab = 'js'"
                                    :class="tab === 'js' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">JavaScript
                                    (Frontend)</button>
                            </div>

                            <!-- Laravel Tab -->
                            <div x-show="tab === 'laravel'" class="space-y-4">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-white">1. Configure <code>bootstrap/app.php</code>
                                    </h4>
                                    <div class="bg-black/50 rounded-lg p-3 border border-white/5 relative group">
                                        <button class="absolute top-2 right-2 text-slate-500 hover:text-white"
                                            onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText)"><i
                                                class="material-icons text-sm">content_copy</i></button>
                                        <pre class="text-xs text-blue-300 font-mono overflow-x-auto p-2">
                                                                            ->withExceptions(function (Exceptions $exceptions) {
                                                                                $exceptions->reportable(function (Throwable $e) {
                                                                                    try {
                                                                                    \Illuminate\Support\Facades\Http::timeout(2)->post('{{ url('/api/v1/capture') }}', [
                                                                                        'api_key' => '{{ $currentStore->api_key }}',
                                                                                        'type' => get_class($e),
                                                                                        'message' => $e->getMessage(),
                                                                                        'file' => $e->getFile(),
                                                                                        'line' => $e->getLine(),
                                                                                        'trace' => $e->getTraceAsString(),
                                                                                        'url' => request()->fullUrl(),
                                                                                        'method' => request()->method(),
                                                                                        'ip' => request()->ip(),
                                                                                    ]);
                                                                                } catch (\Throwable $loggingError) {}
                                                                            });
                                                                        })->create();</pre>
                                    </div>
                                </div>
                            </div>

                            <!-- JS Tab -->
                            <div x-show="tab === 'js'" class="space-y-4">
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-white">1. Add to Layout (<code>app.blade.php</code>)
                                    </h4>
                                    <div class="bg-black/50 rounded-lg p-3 border border-white/5 relative group">
                                        <button class="absolute top-2 right-2 text-slate-500 hover:text-white"
                                            onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText)"><i
                                                class="material-icons text-sm">content_copy</i></button>
                                        <pre class="text-xs text-blue-300 font-mono overflow-x-auto p-2">
                                                                        &lt;script src="{{ url('/js/tracking.js') }}"&gt;&lt;/script&gt;
                                                                        &lt;script&gt;
                                                                            if (typeof ErrorTracker !== 'undefined') {
                                                                                ErrorTracker.init({
                                                                                    endpoint: '{{ url('/api/v1/capture') }}',
                                                                                    apiKey: '{{ $currentStore->api_key }}',
                                                                                    debug: true
                                                                                });
                                                                            }
                                                                        &lt;/script&gt;</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </dialog>

            </div>
        </div>

        <script>
            function errorMonitor(errors) {
                return {
                    search: '',
                    tab: 'all',
                    selectedError: null,
                    errors: errors,
                    get filteredErrors() {
                        return this.errors.filter(error => {
                            const searchLower = this.search.toLowerCase();
                            const matchesSearch = error.message.toLowerCase().includes(searchLower) ||
                                (error.type && error.type.toLowerCase().includes(searchLower)) ||
                                (error.file && error.file.toLowerCase().includes(searchLower));

                            const matchesTab = this.tab === 'all' || error.status === this.tab;
                            return matchesSearch && matchesTab;
                        });
                    },
                    init() {
                        // Pre-select first error if available (optional)
                        // if (this.errors.length > 0) this.selectedError = this.errors[0];
                    }
                };
            }
        </script>
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(30, 41, 59, 0.5);
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(71, 85, 105, 0.8);
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(100, 116, 139, 1);
            }
        </style>
@endsection