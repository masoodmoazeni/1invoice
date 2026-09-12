<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public $id;
    public $label;
    public $checked;
    public $name;
    public $value;
    public $required;
    public $disabled;
    public $class;

    public function __construct(
        $id = null,
        $label = '',
        $checked = false,
        $name = null,
        $value = '1',
        $required = false,
        $disabled = false,
        $class = ''
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->checked = $checked;
        $this->name = $name;
        $this->value = $value;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.form.checkbox');
    }
}