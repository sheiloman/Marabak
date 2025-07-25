<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;

class ScanPage extends Component
{
    #[Layout('livewire.components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.scan-page');
    }
}
