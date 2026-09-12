<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DatePicker extends Component
{
    public $name;
    public $label;
    public $value;
    public $placeholder;
    public $id;

    public function __construct(
        $name,
        $label = null,
        $value = null,
        $placeholder = 'Pick a date',
        $id = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = old($name, $value);
        $this->placeholder = $placeholder;
        $this->id = $id ?? 'kt_datepicker_1';
    }

    public function render()
    {
        return view('components.form.date-picker');
    }
}