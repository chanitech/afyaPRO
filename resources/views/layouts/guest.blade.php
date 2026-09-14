<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('adminlte.title', 'AfyaPRO') }}</title>

        @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
        @include('adminlte::partials.theme-colors')
    </head>
    <body class="login-page bg-body-secondary">
        <div class="login-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="{{ url('/') }}" class="h1">
                        {!! config('adminlte.logo', '<b>Afya</b>PRO') !!}
                    </a>
                </div>
                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
