@props([
    'name',
    'label' => null,
    'rows' => 3,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'icon' => null,
    'help' => null
])

<div class="mb-3">
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
    
    <textarea name="{{ $name }}" 
              id="{{ $name }}"
              rows="{{ $rows }}"
              class="form-control @error($name) is-invalid @enderror"
              placeholder="{{ $placeholder }}"
              {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif
</div>