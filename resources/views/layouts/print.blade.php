<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Print' }} · {{ config('adminlte.title', 'AfyaPRO') }}</title>

        <style>
            body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 24px; background: #e9ecef; }
            .print-toolbar { max-width: 420px; margin: 0 auto 16px; text-align: right; }
            .print-toolbar button { border: 0; background: #0d6efd; color: #fff; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
            .print-toolbar a { margin-right: 12px; color: #495057; text-decoration: none; }
            @media print {
                body { background: #fff; padding: 0; }
                .print-toolbar { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="print-toolbar">
            <a href="{{ route('patients.index') }}">&larr; Back to Patients</a>
            <button type="button" onclick="window.print()">Print</button>
        </div>

        {{ $slot }}
    </body>
</html>
