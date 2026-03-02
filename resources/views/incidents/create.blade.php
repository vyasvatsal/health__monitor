<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2
                class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400 leading-tight">
                {{ __('Report New Incident') }}
            </h2>
            <a href="{{ route('incidents.index') }}"
                class="flex items-center gap-2 text-sm text-slate-300 hover:text-white bg-slate-800/80 hover:bg-slate-700 px-4 py-2 rounded-xl transition-all border border-white/5 shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-8 flex-1 flex flex-col">
        <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex-1 flex flex-col space-y-8">
            <div
                class="bg-slate-900/40 backdrop-blur-xl overflow-hidden rounded-2xl border border-white/5 shadow-2xl relative">
                <div
                    class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-500/30 to-transparent opacity-50">
                </div>
                <div class="p-8">

                    <form method="POST" action="{{ route('incidents.store') }}" class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Incident Title')"
                                class="text-slate-300 font-bold mb-2 ml-1" />
                            <input id="title"
                                class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-slate-600 transition-all outline-none"
                                type="text" name="title" :value="old('title')" required autofocus
                                placeholder="e.g., API Gateway Latency Spike" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description & Details')"
                                class="text-slate-300 font-bold mb-2 ml-1" />
                            <textarea id="description" name="description"
                                class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-slate-600 transition-all outline-none resize-none"
                                rows="5" required
                                placeholder="Describe the symptoms, affected systems, and initial observations...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Severity -->
                            <div>
                                <x-input-label for="severity" :value="__('Severity Level')"
                                    class="text-slate-300 font-bold mb-2 ml-1" />
                                <div class="relative">
                                    <select id="severity" name="severity"
                                        class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white transition-all outline-none appearance-none">
                                        <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}
                                            class="bg-slate-900">Minor</option>
                                        <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}
                                            class="bg-slate-900">Major</option>
                                        <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}
                                            class="bg-slate-900">Critical</option>
                                        <option value="maintenance" {{ old('severity') == 'maintenance' ? 'selected' : '' }} class="bg-slate-900">Maintenance</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('severity')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Initial Status')"
                                    class="text-slate-300 font-bold mb-2 ml-1" />
                                <div class="relative">
                                    <select id="status" name="status"
                                        class="w-full bg-slate-950/50 border border-slate-700/50 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white transition-all outline-none appearance-none">
                                        <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}
                                            class="bg-slate-900">Open</option>
                                        <option value="investigating" {{ old('status') == 'investigating' ? 'selected' : '' }} class="bg-slate-900">Investigating</option>
                                        <option value="identified" {{ old('status') == 'identified' ? 'selected' : '' }}
                                            class="bg-slate-900">Identified</option>
                                        <option value="monitoring" {{ old('status') == 'monitoring' ? 'selected' : '' }}
                                            class="bg-slate-900">Monitoring</option>
                                        <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}
                                            class="bg-slate-900">Resolved</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="pt-6 border-t border-white/5 mt-8 flex justify-end gap-4">
                            <button type="submit"
                                class="flex items-center gap-2 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white font-bold py-3 px-8 rounded-xl shadow-[0_0_20px_rgba(239,68,68,0.3)] transition-all hover:scale-[1.02]">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ __('Report Incident') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>