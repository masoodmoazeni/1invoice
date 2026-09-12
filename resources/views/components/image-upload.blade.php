@props([
    'name' => 'avatar',
    'id' => 'avatar',
    'currentImage' => null,
    'showChange' => true,
    'showCancel' => true,
    'showRemove' => true,
    'showToolbar' => null,
    'wrapperClass' => '',
    'imageClass' => 'w-125px h-125px',
    'defaultImage' => ''
])

@php
    if ($showToolbar !== null) {
        $showChange = $showToolbar;
        $showCancel = $showToolbar;
        $showRemove = $showToolbar;
    }
@endphp

<div class="image-input image-input-outline {{ $wrapperClass }}" data-kt-image-input="true" style="background-image: url({{ asset($defaultImage) }})">
    
    <div class="image-input-wrapper {{ $imageClass }}" style="background-image: url({{ $currentImage ? asset($currentImage) : asset($defaultImage) }})"></div>
    
    @if($showChange)
    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
        <i class="bi bi-pencil-fill fs-7"></i>
        <input type="file" name="{{ $name }}" id="{{ $id }}" accept=".png, .jpg, .jpeg">
    </label>
    @endif
    
    @if($showCancel)
    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
        <i class="bi bi-x fs-2"></i>
    </span>
    @endif
    
    @if($showRemove)
    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
        <i class="bi bi-x fs-2"></i>
    </span>
    @endif
</div>