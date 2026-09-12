<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Alert extends Component
{
    public string $type;
    public ?string $title;
    public bool $dismissible;
    public mixed $message;
    public mixed $errors;
    public bool $autoHide;
    public int $hideAfter;
    public ?string $icon;
    public ?string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type = 'info',
        ?string $title = null,
        bool $dismissible = true,
        mixed $message = null,
        mixed $errors = null,
        bool $autoHide = false,
        int $hideAfter = 5000,
        ?string $icon = null,
        ?string $class = null
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->dismissible = $dismissible;
        $this->message = $message;
        $this->errors = $errors;
        $this->autoHide = $autoHide;
        $this->hideAfter = $hideAfter;
        $this->icon = $icon;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alerts.alert');
    }
    
    /**
     * Get alert icon based on type
     */
    public function getIcon(): string
    {
        return match($this->type) {
            'success' => 'fa-check-circle',
            'danger', 'error' => 'fa-exclamation-triangle',
            'warning' => 'fa-exclamation-circle',
            'info' => 'fa-info-circle',
            'primary' => 'fa-bell',
            'secondary' => 'fa-circle-info',
            'dark' => 'fa-moon',
            default => 'fa-bell'
        };
    }
    
    /**
     * Get alert color class
     */
    public function getColorClass(): string
    {
        return match($this->type) {
            'success' => 'alert-success',
            'danger', 'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            'primary' => 'alert-primary',
            'secondary' => 'alert-secondary',
            'dark' => 'alert-dark',
            default => 'alert-info'
        };
    }
    
    /**
     * Get default title based on type
     */
    public function getDefaultTitle(): string
    {
        return match($this->type) {
            'success' => __('messages.global.alert.success'),
            'danger', 'error' => __('messages.global.alert.danger'),
            'warning' => __('messages.global.alert.warning'),
            'info' => __('messages.global.alert.info'),
            'primary' => __('messages.global.alert.primary'),
            'secondary' => __('messages.global.alert.secondary'),
            'dark' => __('messages.global.alert.dark'),
            default => __('messages.global.alert.dark'),
        };
    }
}