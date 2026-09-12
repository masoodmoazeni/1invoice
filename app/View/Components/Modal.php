<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $id;
    public $title;
    public $size;
    public $submitUrl;
    public $submitMethod;
    public $submitButtonText;
    public $cancelButtonText;
    public $centered;
    public $scrollable;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        $id = 'kt_modal',
        $title = 'Modal Title',
        $size = 'md', // sm, md, lg, xl, fullscreen
        $submitUrl = '#',
        $submitMethod = 'POST',
        $submitButtonText = 'Submit',
        $cancelButtonText = 'Cancel',
        $centered = true,
        $scrollable = false
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
        $this->submitUrl = $submitUrl;
        $this->submitMethod = $submitMethod;
        $this->submitButtonText = $submitButtonText;
        $this->cancelButtonText = $cancelButtonText;
        $this->centered = $centered;
        $this->scrollable = $scrollable;
    }
    
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.modal');
    }
    
    /**
     * Get modal size class
     */
    public function sizeClass()
    {
        return match($this->size) {
            'sm' => 'mw-400px',
            'md' => 'mw-650px',
            'lg' => 'mw-800px',
            'xl' => 'mw-1100px',
            'fullscreen' => 'mw-100 mw-100vw mh-100 mh-100vh',
            default => 'mw-650px',
        };
    }
}