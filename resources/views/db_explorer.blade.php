@extends('layouts.app')

@section('content')
    <div class="py-6 flex-1 flex flex-col">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight flex items-center gap-3">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 1.105 2.239 2 5 2s5-.895 5-2V7M4 7c0 1.105 2.239 2 5 2s5-.895 5-2M4 7c0-1.105 2.239-2 5-2s5 .895 5 2m0 5c0 1.105-2.239 2-5 2s-5-.895-5-2" />
                        </svg>
                        Database Explorer: {{ $currentStore->name }}
                    </h2>
                    <p class="text-slate-400 text-sm mt-1">Inspecting remote database schema and structure.</p>
                </div>

                <a href="{{ route('dashboard', ['store_id' => $currentStore->id]) }}"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg border border-slate-700 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            @if(!$latestSchema)
                <div class="bg-[#1e293b] rounded-2xl border border-white/10 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 7v10c0 1.105 2.239 2 5 2s5-.895 5-2V7M4 7c0 1.105 2.239 2 5 2s5-.895 5-2M4 7c0-1.105 2.239-2 5-2s5 .895 5 2m0 5c0 1.105-2.239 2-5 2s-5-.895-5-2" />
                    </svg>
                    <h3 class="text-xl font-bold text-white">No Schema Captured Yet</h3>
                    <p class="text-slate-400 mt-2 max-w-sm mx-auto">
                        Run <code class="bg-slate-800 text-cyan-400 px-2 py-0.5 rounded">php artisan aihealth:sync-schema</code>
                        in your client project to capture and sync the database structure.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Sidebar: Table List -->
                    <div
                        class="lg:col-span-1 bg-[#1e293b] rounded-2xl border border-white/10 overflow-hidden flex flex-col h-[70vh]">
                        <div class="p-4 border-b border-white/5 bg-slate-800/50">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Tables
                                ({{ count($latestSchema->schema_json) }})</h3>
                        </div>
                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            @foreach($latestSchema->schema_json as $table)
                                <button onclick="showTable('{{ $table['table'] }}')"
                                    class="w-full text-left px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-300 text-sm font-medium">{{ $table['table'] }}</span>
                                        <span
                                            class="text-[10px] text-slate-500 font-mono opacity-0 group-hover:opacity-100">{{ count($table['columns']) }}
                                            cols</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Main: Table Detail -->
                    <div class="lg:col-span-3 bg-[#1e293b] rounded-2xl border border-white/10 flex flex-col h-[70vh]">
                        <div id="no-table-selected" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                            <div
                                class="w-16 h-16 bg-cyan-500/10 rounded-full flex items-center justify-center text-cyan-400 mb-4 border border-cyan-500/20">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Select a table to view details</h3>
                            <p class="text-slate-500 text-sm mt-1">Select any table from the sidebar to inspect its columns and
                                types.</p>
                        </div>

                        @foreach($latestSchema->schema_json as $table)
                            <div id="table-{{ $table['table'] }}" class="table-detail hidden flex-1 flex flex-col overflow-hidden">
                                <div class="p-6 border-b border-white/5 flex justify-between items-center bg-slate-800/20">
                                    <div>
                                        <h3 class="text-2xl font-bold text-white">{{ $table['table'] }}</h3>
                                        <p class="text-xs text-slate-500 font-mono mt-1">Remote Schema Definition</p>
                                    </div>
                                    <div class="px-3 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-full">
                                        <span class="text-[10px] font-bold text-cyan-400 font-mono">{{ count($table['columns']) }}
                                            COLUMNS</span>
                                    </div>
                                </div>
                                <div class="flex-1 overflow-y-auto p-0">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-slate-900/50">
                                                <th
                                                    class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-white/5">
                                                    Column Name</th>
                                                <th
                                                    class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-white/5 text-right">
                                                    Data Type</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @foreach($table['columns'] as $column)
                                                <tr class="hover:bg-white/[0.02] transition-colors">
                                                    <td class="px-6 py-4">
                                                        <span class="text-slate-200 font-medium text-sm">{{ $column['name'] }}</span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <span
                                                            class="px-2 py-0.5 rounded bg-slate-800 text-cyan-400 text-[10px] font-mono border border-slate-700 uppercase">
                                                            {{ $column['type'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex justify-between items-center text-[10px] text-slate-500 font-mono">
                    <span>SNAPSHOT RECORDED: {{ $latestSchema->occurred_at->format('M d, Y H:i:s') }}</span>
                    <span>VERSION HASH: {{ $latestSchema->version_hash }}</span>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showTable(tableName) {
            // Hide welcome state
            document.getElementById('no-table-selected').classList.add('hidden');

            // Hide all table details
            document.querySelectorAll('.table-detail').forEach(el => el.classList.add('hidden'));

            // Show selected table
            const target = document.getElementById('table-' + tableName);
            if (target) {
                target.classList.remove('hidden');
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
@endsection