<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $type;        // 'submit', 'reset', 'button', 'link'
    public $variant;     // 'primary', 'light', 'danger', 'success', 'warning'
    public $text;
    public $id;
    public $icon;
    public $class;
    public $isSubmit;
    public $isReset;
    public $isLink;
    public $href;
    public $target; // '_blank', '_self', '_parent', '_top'
    public $dataId;
    public $dataBsToggle;
    public $dataBsTarget;

    /**
     * Create a new component instance.
     * 
     * @param string $type type of button: 'submit', 'reset', 'button', 'link'
     * @param string $text text of button
     * @param string $id id button
     * @param string $icon icon button
     * @param string $class extra class
     * @param string $variant style button: 'primary', 'light', 'danger', 'success', 'warning'
     * @param string $href link URL (only for type='link')
     * @param string $target link target (only for type='link')
     * @param string $dataId bootstrap toggle (for modal)
     * @param string $dataBsToggle bootstrap toggle (for modal)
     * @param string $dataBsTarget bootstrap target (for modal)
     */
    public function __construct(
        $type = 'submit',
        $text = null,
        $id = null,
        $icon = null,
        $class = null,
        $variant = null,
        $href = null,
        $target = '_self',
        $dataBsToggle = null,
        $dataId = null,
        $dataBsTarget = null
    ) {
        $this->type = $type;
        $this->icon = $icon;
        $this->class = $class;
        $this->href = $href;
        $this->target = $target;
        $this->isLink = $type === 'link';
        $this->dataId = $dataId;
        $this->dataBsToggle = $dataBsToggle;
        $this->dataBsTarget = $dataBsTarget;
        
        if ($text) {
            $this->text = $text;
        } else {
            $this->text = match($type) {
                'reset' => __('messages.global.button.cancel'),
                'submit' => __('messages.global.button.save'),
                'link' => __('messages.global.button.link'),
                default => __('messages.global.button.submit'),
            };
        }
        
        if ($id) {
            $this->id = $id;
        } else {
            $this->id = match($type) {
                'submit' => 'kt_account_profile_details_submit',
                'link' => null,
                default => null,
            };
        }
        
        if ($variant) {
            $this->variant = $variant;
        } else {
            $this->variant = match($type) {
                'reset' => 'light',
                'submit' => 'primary',
                'link' => 'link',
                default => 'primary',
            };
        }
        
        $this->isSubmit = $type === 'submit';
        $this->isReset = $type === 'reset';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.button');
    }
}