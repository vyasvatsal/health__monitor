<section>
    <header>
        <h2 class="text-xl font-bold text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __("Ensure your account is using a long, random password to stay secure.") }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-medium text-slate-300 mb-2">Current Password</label>
            <input id="current_password" name="current_password" type="password"
                class="w-full bg-[#0f172a] border border-slate-700 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition-colors shadow-sm"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">New Password</label>
            <input id="password" name="password" type="password"
                class="w-full bg-[#0f172a] border border-slate-700 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition-colors shadow-sm"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirm
                Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                class="w-full bg-[#0f172a] border border-slate-700 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition-colors shadow-sm"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg shadow-emerald-500/20 transition-all hover:shadow-emerald-500/30 active:scale-95 flex items-center gap-2">
                <i class="material-icons text-[18px]">save</i>
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-400 font-medium flex items-center gap-1">
                    <i class="material-icons text-[16px]">check_circle</i>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>