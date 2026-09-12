@props([
    'type' => 'button',
    'variant' => 'primary',
    'text' => null,
    'id' => null,
    'icon' => null,
    'class' => null,
    'href' => null,
    'target' => '_self',
    'dataId' => null,
    'dataBsToggle' => null,
    'dataBsTarget' => null
])

@if($type === 'link' && $href)
    <a  style="margin-right:5px; margin-left:5px;"
        href="{!! $href !!}"
        target="{{ $target }}"
        @if($id) id="{{ $id }}" @endif
        class="btn btn-{{ $variant }} {{ $class }}"
        @if($icon) data-icon="{{ $icon }}" @endif
        @if($dataId) data-id="{{ $dataId }}" @endif
        @if($dataBsToggle) data-bs-toggle="{{ $dataBsToggle }}" @endif
        @if($dataBsTarget) data-bs-target="{{ $dataBsTarget }}" @endif
    >
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif

        @if($text)
            <span>{{ $text }}</span>
        @endif
    </a>
@else
    <button style="margin-right:5px; margin-left:5px;"
        type="{{ $type }}"
        @if($id) id="{{ $id }}" @endif
        class="btn btn-{{ $variant }} {{ $class }}"
        @if($icon) data-icon="{{ $icon }}" @endif
        @if($dataId) data-id="{{ $dataId }}" @endif
        @if($dataBsToggle) data-bs-toggle="{{ $dataBsToggle }}" @endif
        @if($dataBsTarget) data-bs-target="{{ $dataBsTarget }}" @endif
    >
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif

        @if($text)
            <span>{{ $text }}</span>
        @endif
    </button>
@endif
