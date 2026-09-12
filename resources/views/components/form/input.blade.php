@props([
    'name',
    'labelClass',
    'id' => $name,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'wrapperClass' => 'mb-3',
    'autocomplete' => 'off'
])

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $name }}" class="form-label {{ $labelClass ?? '' }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input type="{{ $type }}"
           name="{{ $name }}" 
           id="{{ $id }}"
           autocomplete="{{ $autocomplete }}"
           {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
           placeholder="{{ $placeholder }}"
           value="{{ old($name, $value) }}"
           {{ $required ? 'required' : '' }}
           {{ $disabled ? 'disabled' : '' }}
           {{ $readonly ? 'readonly' : '' }}>
    
    @if($help && !$errors->has($name))
        <small class="form-text text-muted">{{ $help }}</small>
    @endif
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>