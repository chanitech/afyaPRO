@extends('adminlte::page')

@section('title', $title ?? config('adminlte.title'))

@if (isset($header))
    @section('content_header')
        {{ $header }}
    @stop
@endif

@section('content')
    {{ $slot }}
@stop
