<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select2 extends Component
{
    public $name;
    public $options;
    public $selected;
    public $placeholder;
    public $label;
    public $id;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name, 
        $options = [], 
        $selected = null, 
        $placeholder = 'Select an option...',
        $label = null,
        $id = null,
        $class = ''
    ) {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->id = $id ?? $name;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.form.select2');
    }
}