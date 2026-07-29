<x-app-layout>
    <x-slot name="header">
        <div class="ff-page-header-actions">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center text-lg font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="ff-page-title text-2xl">{{ __('Profile') }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('Manage your account settings') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="ff-card">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="ff-card">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="ff-danger-zone">
                <h3 class="ff-section-header text-red-700 mb-2">{{ __('Danger Zone') }}</h3>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
