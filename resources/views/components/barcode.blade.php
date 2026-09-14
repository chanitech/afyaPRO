@props(['value'])

<span {{ $attributes->merge(['class' => 'barcode-code39']) }}>*{{ strtoupper($value) }}*</span>
