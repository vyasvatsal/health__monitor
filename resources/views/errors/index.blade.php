@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('stores.show', $store) }}"
                        class="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-1">
                        <i class="material-icons text-base">arrow_back</i> Back to Store
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Error Tracking</h1>
                <p class="text-slate-400 text-sm mt-1">Monitor issues for <span
                        class="text-emerald-400">{{ $store->name }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openSetupModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors border border-white/10 flex items-center gap-2">
                    <i class="material-icons text-sm">code</i> Integration Guide
                </button>
            </div>
        </div>

        <!-- Error List -->
        <div class="bg-slate-900/50 backdrop-blur-xl rounded-2xl border border-white/5 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/5">
                            <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Error</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider text-right">
                                Events</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider text-right">Last
                                Seen</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider text-right">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($errorGroups as $group)
                            <tr class="hover:bg-white/5 transition-colors group cursor-pointer"
                                onclick="openErrorModal('{{ $group->id }}')">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-white font-medium text-sm truncate max-w-xs group-hover:text-emerald-400 transition-colors"
                                            title="{{ $group->type }}">{{ $group->type }}</span>
                                        <span
                                            class="text-slate-400 text-xs truncate max-w-xs mt-1">{{ Str::limit($group->message, 60) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-slate-300 text-sm font-mono truncate max-w-[200px]"
                                            title="{{ $group->file }}">{{ basename($group->file) }}</span>
                                        <span class="text-slate-500 text-xs font-mono">Line {{ $group->line }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-white/10">
                                        {{ number_format($group->occurrences_count) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-slate-400 text-sm">{{ $group->last_seen_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($group->status === 'resolved')
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/10">
                                            Resolved
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-500/10 text-red-500 border border-red-500/10 animate-pulse">
                                            Unresolved
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="material-icons text-emerald-400 text-3xl mb-4">check_circle</i>
                                        <p class="text-lg text-white font-medium">No errors detected</p>
                                        <button onclick="openSetupModal()"
                                            class="mt-4 text-emerald-400 hover:text-emerald-300 text-sm font-medium hover:underline">
                                            Installation Instructions
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($errorGroups->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-slate-900/30">
                    {{ $errorGroups->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Details Modal -->
    <dialog id="error-modal"
        class="bg-transparent backdrop:bg-black/80 backdrop:backdrop-blur-sm p-0 w-full max-w-4xl rounded-2xl shadow-2xl open:animate-fade-in">
        <div class="bg-slate-900 border border-white/10 rounded-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between p-4 border-b border-white/5">
                <h3 class="text-lg font-bold text-white">Error Details</h3>
                <button onclick="document.getElementById('error-modal').close()"
                    class="text-slate-400 hover:text-white transition-colors">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div id="error-modal-content" class="p-6 overflow-y-auto">
                <!-- Content loaded via AJAX -->
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4">
                    </div>
                    <span class="text-slate-400">Loading details...</span>
                </div>
            </div>
        </div>
    </dialog>

    <!-- Setup Modal -->
    <dialog id="setup-modal"
        class="bg-transparent backdrop:bg-black/80 backdrop:backdrop-blur-sm p-0 w-full max-w-2xl rounded-2xl shadow-2xl open:animate-fade-in">
        <div class="bg-slate-900 border border-white/10 rounded-2xl flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-white/5">
                <h3 class="text-lg font-bold text-white">Integration Guide</h3>
                <button onclick="document.getElementById('setup-modal').close()"
                    class="text-slate-400 hover:text-white transition-colors">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="p-6 space-y-6">

                <!-- Tab Navigation (Simple) -->
                <div x-data="{ tab: 'js' }">
                    <div class="flex border-b border-white/10 mb-4">
                        <button @click="tab = 'js'"
                            :class="tab === 'js' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-200'"
                            class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">Vanilla JS</button>
                        <button @click="tab = 'laravel'"
                            :class="tab === 'laravel' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-200'"
                            class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">Laravel</button>
                    </div>

                    <!-- JS Tab -->
                    <div x-show="tab === 'js'" class="space-y-4">
                        <p class="text-sm text-slate-400">Add this snippet to your HTML head to capture global, promise,
                            network, and resource errors.</p>
                        <div class="bg-black/50 rounded-lg p-3 border border-white/5 relative group">
                            <button class="absolute top-2 right-2 text-slate-500 hover:text-white"
                                onclick="navigator.clipboard.writeText(this.parentElement.innerText.trim())"><i
                                    class="material-icons text-sm">content_copy</i></button>
                            <pre class="text-xs text-slate-300 font-mono overflow-x-auto p-2">
        &lt;script src="{{ asset('js/monitor-client.js') }}"&gt;&lt;/script&gt;
        &lt;script&gt;
            HealthMonitor.init({
                endpoint: '{{ route('api.capture', ['store_id' => $store->id]) }}',
                publicKey: '{{ $store->public_key }}'
            });
        &lt;/script&gt;</pre>
                        </div>
                    </div>

                    <!-- Laravel Tab -->
                    <div x-show="tab === 'laravel'" class="space-y-4">
                        <p class="text-sm text-slate-400">For backend error tracking, hook into Laravel's exception handler
                            (e.g., in <code>bootstrap/app.php</code>).</p>
                        <div class="bg-black/50 rounded-lg p-3 border border-white/5 relative group">
                            <button class="absolute top-2 right-2 text-slate-500 hover:text-white"
                                onclick="navigator.clipboard.writeText(this.parentElement.innerText.trim())"><i
                                    class="material-icons text-sm">content_copy</i></button>
                            <pre class="text-xs text-slate-300 font-mono overflow-x-auto p-2">
        // In bootstrap/app.php (Laravel 11)
        -&gt;withExceptions(function (Exceptions $exceptions) {
            $exceptions-&gt;reportable(function (Throwable $e) {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'X-Monitor-Key' =&gt; '{{ $store->secret_key }}'
                ])-&gt;post('{{ route('api.capture', ['store_id' => $store->id]) }}', [
                    'exception' =&gt; [
                        'type' =&gt; get_class($e),
                        'message' =&gt; $e-&gt;getMessage(),
                        'file' =&gt; $e-&gt;getFile(),
                        'line' =&gt; $e-&gt;getLine(),
                        'trace' =&gt; $e-&gt;getTraceAsString()
                    ],
                    'context' =&gt; [
                        'url' =&gt; request()-&gt;fullUrl(),
                        'method' =&gt; request()-&gt;method(),
                    ]
                ]);
            });
        })</pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </dialog>

    <script>
        function openSetupModal() {
            document.getElementById('setup-modal').showModal();
        }

        function openErrorModal(groupId) {
            const modal = document.getElementById('error-modal');
            const content = document.getElementById('error-modal-content');

            modal.showModal();

            // Reset content
            content.innerHTML = `
                     <div class="flex flex-col items-center justify-center py-12">
                        <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                        <span class="text-slate-400">Loading details...</span>
                    </div>
                `;

            // Fetch details
            fetch(`{{ url('stores/' . $store->id . '/errors') }}/${groupId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(err => {
                    content.innerHTML = `<div class="text-red-400 p-4">Failed to load error details.</div>`;
                });
        }
    </script>
@endsection