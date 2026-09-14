<x-app-layout>
    <x-slot name="header">
        <h3 class="mb-0">{{ __('Profile') }}</h3>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <x-adminlte-card title="{{ __('Profile Information') }}">
                <livewire:profile.update-profile-information-form />
            </x-adminlte-card>

            <x-adminlte-card title="{{ __('Update Password') }}">
                <livewire:profile.update-password-form />
            </x-adminlte-card>

            <x-adminlte-card title="{{ __('Delete Account') }}" theme="danger" outline>
                <livewire:profile.delete-user-form />
            </x-adminlte-card>
        </div>
    </div>
</x-app-layout>
