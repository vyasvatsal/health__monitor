<x-settings-layout>
    <div class="space-y-6">

        <!-- Profile Info -->
        <div class="p-6 sm:p-8 bg-[#1e293b] border border-white/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-bold text-white">
                        {{ __('Profile Information') }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ __("Update your account's profile information and email address.") }}
                    </p>
                </header>
                <div class="mt-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-6 sm:p-8 bg-[#1e293b] border border-white/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-bold text-white">
                        {{ __('Update Password') }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </p>
                </header>
                <div class="mt-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="p-6 sm:p-8 bg-[#1e293b] border border-red-500/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                <header>
                    <h2 class="text-lg font-bold text-white">
                        {{ __('Delete Account') }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                    </p>
                </header>
                <div class="mt-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

    </div>
</x-settings-layout>