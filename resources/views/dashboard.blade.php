<x-app-layout>
    <x-slot name="header">
        <h3 class="mb-0">{{ __('Dashboard') }}</h3>
    </x-slot>

    <x-adminlte-alert theme="success" icon="bi bi-check-circle">
        {{ __('Signed in as :name.', ['name' => auth()->user()->name]) }}
    </x-adminlte-alert>
</x-app-layout>
