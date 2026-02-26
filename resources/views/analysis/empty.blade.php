<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Deep AI Scan (GTmetrix Analysis) - {{ $store->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#0f172a] min-h-screen text-slate-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#1e293b] border border-white/10 rounded-xl p-12 text-center shadow-xl">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-emerald-500 rounded-2xl mx-auto flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/20">
                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>

                <h3 class="text-3xl font-bold text-white mb-4">No Deep Analysis Found</h3>
                <p class="text-slate-400 max-w-xl mx-auto mb-8 text-lg">
                    Run an intensive Lighthouse Performance, Accessibility, SEO, and AI structural scan to get
                    actionable insights on how to improve <strong>{{ $store->name }}'s</strong> conversions.
                </p>

                <form method="POST" action="{{ route('analysis.store', $store->id) }}">
                    @csrf
                    @if(session('error'))
                        <div
                            class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-lg mb-6 max-w-md mx-auto">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto">
                        <input type="url" name="url"
                            value="{{ $store->domain ? (str_starts_with($store->domain, 'http') ? $store->domain : 'https://' . $store->domain) : '' }}"
                            placeholder="https://example.com" required
                            class="w-full bg-[#0f172a] border border-white/10 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-slate-600">

                        <button type="submit"
                            onclick="this.innerHTML='Scanning... Please wait up to 60s'; this.classList.add('opacity-75', 'cursor-not-allowed');"
                            class="whitespace-nowrap bg-emerald-500 hover:bg-emerald-400 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Generate Report
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-4 uppercase tracking-widest">Powered by Google Lighthouse &
                        Gemini AI</p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>