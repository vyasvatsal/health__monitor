<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Alert Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 flex-1 flex flex-col">
        <div class="max-w-2xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10 p-8">
                <form method="POST" action="{{ route('settings.alerts.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                            <span class="text-xl">🔔</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Notification Preferences</h3>
                            <p class="text-sm text-slate-400">Manage how and when you receive alerts.</p>
                        </div>
                    </div>

                    <div class="space-y-6">

                        <!-- Critical Alerts -->
                        <div
                            class="flex items-start p-4 rounded-lg bg-white/5 border border-white/5 hover:border-white/10 transition-colors">
                            <div class="flex items-center h-5 mt-1">
                                <input id="email_critical" name="email_critical" type="checkbox" value="1"
                                    class="w-5 h-5 bg-[#0f172a] border-slate-600 rounded text-emerald-500 focus:ring-emerald-500 focus:ring-offset-[#1e293b] focus:ring-offset-2"
                                    {{ ($settings['email_critical'] ?? true) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-4 text-sm">
                                <label for="email_critical" class="font-medium text-white block mb-1">Critical Alerts
                                    (Instant)</label>
                                <p class="text-slate-400 leading-relaxed">Receive an email immediately when any monitor
                                    reports a "Critical" status or failure.</p>
                            </div>
                        </div>

                        <!-- Daily Digest -->
                        <div
                            class="flex items-start p-4 rounded-lg bg-white/5 border border-white/5 opacity-50 cursor-not-allowed">
                            <div class="flex items-center h-5 mt-1">
                                <input id="email_digest" name="email_digest" type="checkbox" value="1" disabled
                                    class="w-5 h-5 bg-[#0f172a] border-slate-700 rounded text-slate-500 cursor-not-allowed">
                            </div>
                            <div class="ml-4 text-sm">
                                <label for="email_digest" class="font-medium text-white block mb-1">Daily Digest (Coming
                                    Soon)</label>
                                <p class="text-slate-500 leading-relaxed">Receive a morning summary of your store's
                                    health score and performance metrics.</p>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end border-t border-white/10 pt-6">
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-2.5 px-6 rounded-lg transition-colors shadow-lg shadow-emerald-900/20 flex items-center gap-2">
                            <span>Save Preferences</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>