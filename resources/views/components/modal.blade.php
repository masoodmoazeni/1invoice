@props([
    'id' => 'kt_modal',
    'title' => 'Modal Title',
    'size' => 'md',
    'submitUrl' => '#',
    'submitMethod' => 'POST',
    'submitButtonText' => 'Submit',
    'cancelButtonText' => 'Cancel',
    'centered' => true,
    'scrollable' => false,
    'formId' => null
])

@php
    $sizeClass = match($size) {
        'sm' => 'mw-400px',
        'md' => 'mw-650px',
        'lg' => 'mw-800px',
        'xl' => 'mw-1100px',
        'fullscreen' => 'mw-100 mw-100vw mh-100 mh-100vh',
        default => 'mw-650px',
    };

    $centeredClass = $centered ? 'modal-dialog-centered' : '';
    $scrollableClass = $scrollable ? 'modal-dialog-scrollable' : '';
    $modalFormId = $formId ?? $id . '_form';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog {{ $centeredClass }} {{ $scrollableClass }} {{ $sizeClass }}">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" id="{{ $id }}_header">
                <h2 class="modal-title">{{ $title }}</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Modal Form -->
            <form id="{{ $modalFormId }}" class="form" action="{{ $submitUrl }}" method="{{ $submitMethod }}">
                <!-- <div class="alerts-container-modal">
                    <x-alert type="success" :auto-hide="true" hide-after="3000" />
                    <x-alert type="danger" :errors="$errors" :dismissible="true" />
                    <x-alert type="warning" :auto-hide="true" hide-after="5000" />
                </div> -->
                @csrf
                @if(strtoupper($submitMethod) !== 'POST')
                    @method($submitMethod)
                @endif

                <!-- Modal Body -->
                <div class="modal-body py-5 px-lg-17">
                    {{ $slot }}
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('{{ $id }}');
        const form = document.getElementById('{{ $modalFormId }}');

        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('{{ $id }}_submit');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.querySelector('.indicator-label')?.classList.add('d-none');
                    submitBtn.querySelector('.indicator-progress')?.classList.remove('d-none');
                }
            });
        }
    });

    // Reset modal state when hidden
    $('#{{ $id }}').on('hidden.bs.modal', function() {
        const submitBtn = document.getElementById('{{ $id }}_submit');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.querySelector('.indicator-label')?.classList.remove('d-none');
            submitBtn.querySelector('.indicator-progress')?.classList.add('d-none');
        }
    });
</script>
@endpush
