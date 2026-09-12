@props(['name', 'label' => null, 'placeholder' => null, 'required' => false, 'value' => '', 'icon' => null, 'help' => null])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    
    <div class="input-group">
        @if($icon)
            <span class="input-group-text">
                <i class="{{ $icon }}"></i>
            </span>
        @endif
        
        <input type="password" 
               name="{{ $name }}" 
               id="{{ $name }}"
               value="{{ $value }}"
               class="form-control @error($name) is-invalid @enderror" 
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}>
               
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('{{ $name }}')">
            <i class="bi bi-eye"></i>
        </button>
        
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    @if($help && !$errors->has($name))
        <div class="form-text text-muted">{{ $help }}</div>
    @endif
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.parentElement.querySelector('button i');
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
    
    if (type === 'text') {
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>