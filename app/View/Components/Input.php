<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public $name;
    public $id;
    public $type;
    public $placeholder;
    public $value;
    public $required;
    public $label;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $name,
        $id,
        $type = 'text',
        $placeholder = '',
        $value = '',
        $required = false,
        $label = ''
        )
    {
        $this->name = $name;
        $this->id = $id;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->required = $required;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
