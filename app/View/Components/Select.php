<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $label;
    public $options;
    public $collection;
    public $optionValue;
    public $optionLabel;
    public $placeholder;
    public $value;
    public $required;
    public $icon;
    public $help;
    public $wrapperClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name, 
        $label = null, 
        $options = [], 
        $collection = null, 
        $optionValue = 'id', 
        $optionLabel = 'name', 
        $placeholder = null, 
        $value = null, 
        $required = false, 
        $icon = null, 
        $help = null, 
        $wrapperClass = 'mb-3'
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->collection = $collection;
        $this->optionValue = $optionValue;
        $this->optionLabel = $optionLabel;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->required = $required;
        $this->icon = $icon;
        $this->help = $help;
        $this->wrapperClass = $wrapperClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}