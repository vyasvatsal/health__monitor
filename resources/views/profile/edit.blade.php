<x-settings-layout>
    <div class="space-y-6">

        <!-- Profile Info -->
        <div class="p-6 sm:p-8 bg-[#1e293b] border border-white/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-6 sm:p-8 bg-[#1e293b] border border-white/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="p-6 sm:p-8 bg-[#1e293b] border border-red-500/10 shadow-sm rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-settings-layout>