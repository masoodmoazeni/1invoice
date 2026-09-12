@props([
    'id' => null,
    'label' => '',
    'checked' => false,
    'name' => null,
    'value' => '1',
    'required' => false,
    'disabled' => false,
    'class' => ''
])

@php
    $name = $name ?? $id;
    $id = $id ?? $name;
@endphp

<div class="form-check form-check-custom form-check-solid {{ $class }}">
    <input
        type="checkbox"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
        @required($required)
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'form-check-input']) }}
    >
    @if($label)
        <label class="form-check-label me-2" for="{{ $id }}">
            {{ $label }}
        </label>
    @endif
</div>
