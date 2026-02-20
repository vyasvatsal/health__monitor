<section>
    <header>
        <h2 class="text-xl font-bold text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Name</label>
            <input id="name" name="name" type="text"
                class="w-full bg-[#0f172a] border border-slate-700 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition-colors shadow-sm"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
            <input id="email" name="email" type="email"
                class="w-full bg-[#0f172a] border border-slate-700 focus:border-emerald-500 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition-colors shadow-sm"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p
                        class="text-sm mt-3 text-amber-500 bg-amber-500/10 border border-amber-500/20 px-3 py-2 rounded-md inline-block">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-slate-300 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 ml-1">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg shadow-emerald-500/20 transition-all hover:shadow-emerald-500/30 active:scale-95 flex items-center gap-2">
                <i class="material-icons text-[18px]">save</i>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-400 font-medium flex items-center gap-1">
                    <i class="material-icons text-[16px]">check_circle</i>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>