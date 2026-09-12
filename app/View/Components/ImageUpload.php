<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ImageUpload extends Component
{
    public $name;
    public $id;
    public $currentImage;
    public $showChange;
    public $showCancel;
    public $showRemove;
    public $showToolbar;
    public $wrapperClass;
    public $imageClass;
    public $defaultImage;

    public function __construct(
        $name = 'image',
        $id = 'avatar',
        $currentImage = null,
        $showChange = true,
        $showCancel = true,
        $showRemove = true,
        $showToolbar = null,
        $wrapperClass = '',
        $imageClass = 'w-125px h-125px',
        $defaultImage = 'assets/media/avatars/blank.png'
    ) {
        $this->name = $name;
        $this->id = $id;
        $this->currentImage = $currentImage;
        $this->showChange = $showChange;
        $this->showCancel = $showCancel;
        $this->showRemove = $showRemove;
        $this->showToolbar = $showToolbar;
        $this->wrapperClass = $wrapperClass;
        $this->imageClass = $imageClass;
        $this->defaultImage = $defaultImage;
    }

    public function render()
    {
        return view('components.image-upload');
    }
}