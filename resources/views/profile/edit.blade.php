<x-app-layout>
    <x-slot name="header">
        Profile Settings
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Profile Info -->
        <div class="p-4 sm:p-8 bg-[#1e293b] border border-white/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                <h3 class="text-lg font-bold text-white mb-4">Profile Information</h3>
                <div class="text-slate-400 text-sm mb-6">Update your account's profile information and email address.
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-4 sm:p-8 bg-[#1e293b] border border-white/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                <h3 class="text-lg font-bold text-white mb-4">Update Password</h3>
                <div class="text-slate-400 text-sm mb-6">Ensure your account is using a long, random password to stay
                    secure.</div>
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="p-4 sm:p-8 bg-[#1e293b] border border-red-500/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                <h3 class="text-lg font-bold text-white mb-4">Delete Account</h3>
                <div class="text-slate-400 text-sm mb-6">Once your account is deleted, all of its resources and data
                    will be permanently deleted.</div>
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-app-layout>