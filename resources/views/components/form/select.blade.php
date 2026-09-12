@props([
    'name',
    'label' => null,
    'options' => [],
    'collection' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'placeholder' => null,
    'value' => null,
    'selected' => null,
    'required' => false,
    'icon' => null,
    'help' => null,
    'wrapperClass' => 'mb-3'
])

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-bold">
            @if($icon)
                <i class="{{ $icon }} me-1 text-primary"></i>
            @endif
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    @php
        $selectedValue = $selected ?? $value ?? old($name);
    @endphp
    
    <select name="{{ $name }}" 
            id="{{ $name }}"
            class="form-select @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}>
        
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @if($collection)
            @foreach($collection as $item)
                <option value="{{ $item->$optionValue }}" 
                    {{ $selectedValue == $item->$optionValue ? 'selected' : '' }}>
                    {{ $item->$optionLabel }}
                </option>
            @endforeach
        @endif
        
        @if($options && !$collection)
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" 
                    {{ $selectedValue == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @endif
        
    </select>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif
</div>