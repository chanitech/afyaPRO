@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => $el.querySelector(\'input, textarea, select, button\')?.focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="modal"
    style="display: none;"
    tabindex="-1"
>
    <div x-show="show" x-on:click="show = false" class="modal-backdrop show"></div>

    <div x-show="show" class="modal-dialog modal-dialog-centered {{ $maxWidth }}">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
