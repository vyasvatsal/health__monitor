<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('New Project') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col items-center justify-center">
        <div class="max-w-xl w-full px-4">
            <div class="bg-[#1e293b] overflow-hidden shadow-2xl rounded-2xl border border-white/10">
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center border border-emerald-500/20 shadow-lg shadow-emerald-500/10">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Create New Project</h3>
                            <p class="text-sm text-slate-400">Set up a new application to monitor.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('stores.store') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-300 mb-2">Project Name</label>
                            <input id="name"
                                class="block w-full rounded-xl bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 focus:ring-opacity-50 transition-all shadow-inner p-3"
                                type="text" name="name" :value="old('name')" required autofocus
                                placeholder="e.g. Production Web App" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Domain -->
                        <div>
                            <label for="domain" class="block text-sm font-bold text-slate-300 mb-2">Domain
                                (Optional)</label>
                            <div class="relative">
                                <input id="domain"
                                    class="block w-full rounded-xl bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 focus:ring-opacity-50 transition-all shadow-inner p-3 pl-10"
                                    type="text" name="domain" :value="old('domain')" placeholder="app.example.com" />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                            <p class="mt-2 text-xs text-slate-500">Used for generating direct links and uptime checks.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-white/5">
                            <a class="text-sm font-bold text-slate-400 hover:text-white transition-colors"
                                href="{{ route('stores.index') }}">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:scale-105 flex items-center gap-2">
                                <span>Create Project</span>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>