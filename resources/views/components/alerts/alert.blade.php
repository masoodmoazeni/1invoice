@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
    'message' => null,
    'errors' => null,
    'autoHide' => false,
    'hideAfter' => 5000,
    'icon' => null,
    'class' => null,
])

@php
    $config = [
        'success' => [
            'icon' => 'fa-check-circle',
            'color' => 'alert-success',
            'default_title' => __('messages.global.alert.success'),
            'bg_gradient' => 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)'
        ],
        'danger' => [
            'icon' => 'fa-exclamation-triangle',
            'color' => 'alert-danger',
            'default_title' => __('messages.global.alert.danger'),
            'bg_gradient' => 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)'
        ],
        'error' => [
            'icon' => 'fa-exclamation-triangle',
            'color' => 'alert-danger',
            'default_title' => __('messages.global.alert.danger'),
            'bg_gradient' => 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)'
        ],
        'warning' => [
            'icon' => 'fa-exclamation-circle',
            'color' => 'alert-warning',
            'default_title' => __('messages.global.alert.warning'),
            'bg_gradient' => 'linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%)'
        ],
        'info' => [
            'icon' => 'fa-info-circle',
            'color' => 'alert-info',
            'default_title' => __('messages.global.alert.info'),
            'bg_gradient' => 'linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%)'
        ],
        'primary' => [
            'icon' => 'fa-bell',
            'color' => 'alert-primary',
            'default_title' => __('messages.global.alert.primary'),
            'bg_gradient' => 'linear-gradient(135deg, #cfe2ff 0%, #b6d4fe 100%)'
        ],
        'secondary' => [
            'icon' => 'fa-circle-info',
            'color' => 'alert-secondary',
            'default_title' => __('messages.global.alert.secondary'),
            'bg_gradient' => 'linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%)'
        ],
        'dark' => [
            'icon' => 'fa-moon',
            'color' => 'alert-dark',
            'default_title' => __('messages.global.alert.dark'),
            'bg_gradient' => 'linear-gradient(135deg, #d3d3d4 0%, #c8c9ca 100%)'
        ]
    ];
    
    $currentConfig = $config[$type] ?? $config['info'];
    
    $alertIcon = $icon ?? $currentConfig['icon'];
    
    $colorClass = $currentConfig['color'];
    
    $displayTitle = $title ?? $currentConfig['default_title'];
    
    $alertMessage = $message;
    
    $hasErrors = false;
    if ($errors && (($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any()) || 
        (is_array($errors) && count($errors) > 0) ||
        ($errors instanceof \Illuminate\Support\MessageBag && $errors->any()))) {
        $hasErrors = true;
    }
    
    $showDangerErrors = in_array($type, ['danger', 'error']) && $hasErrors;
    
    $sessionSuccess = session('success');
    $sessionError = session('error');
    $sessionWarning = session('warning');
    $sessionInfo = session('info');
    
    if (!$alertMessage && !$showDangerErrors) {
        switch ($type) {
            case 'success':
                $alertMessage = $sessionSuccess;
                break;
            case 'danger':
            case 'error':
                $alertMessage = $sessionError;
                break;
            case 'warning':
                $alertMessage = $sessionWarning;
                break;
            case 'info':
                $alertMessage = $sessionInfo;
                break;
        }
    }
    
    $animationClass = $autoHide && $dismissible ? 'alert-animated' : '';
    $customClass = $class ?? '';
    
    $shouldShow = ($showDangerErrors) || 
                  ($alertMessage && !$showDangerErrors) || 
                  (!$alertMessage && !$showDangerErrors && !$slot->isEmpty());
@endphp

@if($shouldShow)
    <div class="alert {{ $colorClass }} {{ $dismissible ? 'alert-dismissible fade show' : '' }} {{ $animationClass }} {{ $customClass }} border-0 rounded-3 shadow-sm" 
         role="alert"
         style="background: {{ $currentConfig['bg_gradient'] }}; border-right: 4px solid rgba(0,0,0,0.1);"
         @if($autoHide && $dismissible)
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => { 
                 show = false; 
                 setTimeout(() => $el.remove(), 300);
             }, {{ $hideAfter }})"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
         @endif>
        
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0 me-3">
                <i class="fas {{ $alertIcon }} fa-2x mt-1"></i>
            </div>
            
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <strong class="d-block mb-2 fs-6">{{ $displayTitle }}</strong>
                    @if($dismissible && !$autoHide)
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="close"></button>
                    @endif
                </div>
                
                @if($showDangerErrors)
                    <div class="alert-errors mt-2">
                        @if($errors instanceof \Illuminate\Support\MessageBag || $errors instanceof \Illuminate\Support\ViewErrorBag)
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li class="mb-1">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @elseif(is_array($errors))
                            <ul class="mb-0 ps-3">
                                @foreach($errors as $error)
                                    <li class="mb-1">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                
                @elseif($alertMessage)
                    <div class="alert-message">
                        @if(is_array($alertMessage))
                            <ul class="mb-0 ps-3">
                                @foreach($alertMessage as $msg)
                                    <li class="mb-1">{{ $msg }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0">{{ $alertMessage }}</p>
                        @endif
                    </div>
                @endif
                
                @if(!$slot->isEmpty())
                    <div class="mt-3 alert-extra-content">
                        {{ $slot }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif