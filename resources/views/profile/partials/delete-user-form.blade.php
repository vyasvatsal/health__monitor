<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __("Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.") }}
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-6 rounded-lg shadow-lg shadow-red-900/20 transition-all hover:scale-[1.02]">{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}"
            class="p-6 bg-[#1e293b] border border-white/10 text-slate-300">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-white">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                {{ __("Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.") }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>

                <input id="password" name="password" type="password"
                    class="w-full bg-slate-950 border border-slate-700/50 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition outline-none"
                    placeholder="{{ __('Password') }}" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')"
                    class="text-slate-400 hover:text-white mr-4 transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                    class="bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-6 rounded-lg shadow-lg shadow-red-900/20 transition-all hover:scale-[1.02]">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>