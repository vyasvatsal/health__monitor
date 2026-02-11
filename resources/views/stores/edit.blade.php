<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Edit Project') }}
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
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Edit Project</h3>
                            <p class="text-sm text-slate-400">Update project details and configuration.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('stores.update', $store) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-300 mb-2">Project Name</label>
                            <input id="name"
                                class="block w-full rounded-xl bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 focus:ring-opacity-50 transition-all shadow-inner p-3"
                                type="text" name="name" :value="old('name', $store->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Domain -->
                        <div>
                            <label for="domain" class="block text-sm font-bold text-slate-300 mb-2">Domain URL</label>
                            <div class="relative">
                                <input id="domain"
                                    class="block w-full rounded-xl bg-[#0f172a] border-slate-700 text-white focus:border-emerald-500 focus:ring-emerald-500 focus:ring-opacity-50 transition-all shadow-inner p-3 pl-10"
                                    type="text" name="domain" :value="old('domain', $store->domain)"
                                    placeholder="https://example.com" />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-white/5">
                            <a class="text-sm font-bold text-slate-400 hover:text-white transition-colors"
                                href="{{ route('stores.show', $store) }}">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:scale-105 flex items-center gap-2">
                                <span>Save Changes</span>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>