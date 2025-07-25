<?php

namespace App\Livewire\Pages\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Modal extends Component
{
    public string $title;
    public bool $showClose = true;

    public function __construct(string $title, bool $showClose = false) {
        $this->title = $title;
        $this->showClose = $showClose;
    }

    public function render(): View|Closure|string
    {
        return view('livewire.components.modal');
    }
}
