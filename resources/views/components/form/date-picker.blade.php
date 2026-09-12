@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => 'Pick a date',
    'id' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => true,
    'help' => null,
    'wrapperClass' => 'mb-3',
    'dateFormat' => 'Y-m-d'
])

@php
    $currentId = $id ?? $name . '_' . uniqid();
    $currentValue = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $currentId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        name="{{ $name }}"
        id="{{ $currentId }}"
        value="{{ $currentValue }}"
        placeholder="{{ $placeholder }}"
        type="text"
        {{ $readonly ? 'readonly="readonly"' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        @php
            $classes = 'form-control form-control-solid ps-12 flatpickr-input';
            if ($hasError) $classes .= ' is-invalid';
        @endphp
        class="{{ $classes }}"
        style="{{ $hasError ? 'border: 1px solid #f1416c !important;' : '' }}"
    />
    
    @if($help && !$hasError)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif
    
    @error($name)
        <div class="invalid-feedback d-block" style="display: block !important;">
            {{ $message }}
        </div>
    @enderror
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById("{{ $currentId }}");
        if (input && typeof flatpickr !== 'undefined') {
            flatpickr(input, {
                dateFormat: "{{ $dateFormat }}"
            });
        }
    });
</script>
@endpush