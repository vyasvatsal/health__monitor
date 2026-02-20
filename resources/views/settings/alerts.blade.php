<x-settings-layout>
    <div class="bg-[#1e293b] overflow-hidden shadow-sm rounded-lg border border-white/10">
        <div class="p-6 border-b border-white/5 bg-slate-900/50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20">
                        <i class="material-icons text-xl">notifications_active</i>
                    </span>
                    Notification Preferences
                </h3>
                <p class="text-sm text-slate-400 mt-2 ml-1">Manage how and when the system ensures you're informed.</p>
            </div>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('settings.alerts.update') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">

                    <!-- Critical Alerts -->
                    <div
                        class="group flex items-start gap-4 p-5 rounded-xl bg-gradient-to-r from-emerald-900/10 to-transparent border border-emerald-500/10 hover:border-emerald-500/30 transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        </div>

                        <div class="flex-shrink-0 mt-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_critical" value="1" class="sr-only peer" {{ ($settings['email_critical'] ?? true) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                                </div>
                            </label>
                        </div>

                        <div class="flex-1">
                            <label class="block text-base font-bold text-white mb-1">Critical Alerts (Instant)</label>
                            <p class="text-slate-400 text-sm leading-relaxed mb-3">
                                Start sending me emails immediately when any monitor reports a <span
                                    class="text-red-400 font-medium">Critical</span> status or system failure.
                            </p>
                            <div class="flex gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 text-xs font-medium text-slate-300 border border-white/5">
                                    <i class="material-icons text-[14px] text-yellow-400">bolt</i> Real-time
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 text-xs font-medium text-slate-300 border border-white/5">
                                    <i class="material-icons text-[14px]">email</i> Email
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Digest -->
                    <div
                        class="group flex items-start gap-4 p-5 rounded-xl bg-slate-800/20 border border-white/5 opacity-75">
                        <div class="flex-shrink-0 mt-1">
                            <label class="relative inline-flex items-center cursor-not-allowed">
                                <input type="checkbox" name="email_digest" value="1" disabled class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-800 rounded-full border border-slate-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-500 after:rounded-full after:h-5 after:w-5">
                                </div>
                            </label>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <label class="block text-base font-bold text-slate-300">Daily Digest</label>
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-blue-500/20 text-blue-400 border border-blue-500/20">Coming
                                    Soon</span>
                            </div>
                            <p class="text-slate-500 text-sm leading-relaxed">
                                Receive a summary every morning at 9:00 AM containing your store's health score,
                                performance trends, and error counts.
                            </p>
                        </div>
                    </div>

                </div>

                <div class="mt-8 flex items-center justify-between pt-6 border-t border-white/5">

                    <!-- Test Alert Section -->
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="document.getElementById('test-alert-form').submit();"
                            class="text-slate-400 hover:text-white text-sm flex items-center gap-2 transition-colors px-3 py-2 rounded-lg hover:bg-white/5">
                            <i class="material-icons text-[18px]">send</i>
                            Send Test Alert
                        </button>
                    </div>

                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-6 rounded-lg transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 flex items-center gap-2 active:scale-95">
                        <i class="material-icons text-[20px]">check</i>
                        <span>Save Preferences</span>
                    </button>
                </div>
            </form>

            <!-- Hidden Form for Test Alert -->
            <form id="test-alert-form" action="{{ route('settings.alerts.test') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</x-settings-layout>