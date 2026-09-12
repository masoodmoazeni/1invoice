@props([
    'name' => '',
    'options' => [],
    'collection' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'selected' => null,
    'placeholder' => 'Select an option...',
    'label' => null,
    'id' => null,
    'class' => '',
    'required' => false,
    'icon' => null,
    'help' => null,
    'wrapperClass' => 'mb-3'
])

<div class="{{ $wrapperClass }}" dir="rtl">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label fw-bold">
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
        $selectedValue = $selected ?? old($name);
    @endphp

    <select
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        data-control="select2"
        data-placeholder="{{ $placeholder }}"
        class="form-select {{ $class }} @error($name) is-invalid @enderror"
        {{ $required ? 'required' : '' }}
    >
        <option value=""></option>

        @if($collection)
            @foreach($collection as $item)
                <option value="{{ $item->$optionValue }}"
                    {{ ($selectedValue == $item->$optionValue) ? 'selected' : '' }}>
                    {{ $item->$optionLabel }}
                </option>
            @endforeach
        @endif

        @if($options && !$collection)
            @foreach($options as $value => $labelText)
                <option value="{{ $value }}"
                    {{ ($selectedValue == $value) ? 'selected' : '' }}>
                    {{ $labelText }}
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#{{ $id ?? $name }}').select2({
            placeholder: "{{ $placeholder }}",
            allowClear: true,
            theme: 'bootstrap-5'
        });
    });
</script>
@endpush
