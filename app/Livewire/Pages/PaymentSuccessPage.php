<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;

class PaymentSuccessPage extends Component
{
    public function mount()
    {
        session()->forget(['external_id', 'has_unpaid_transaction', 'cart_items', 'payment_token']);
        session()->save();
    }

    #[Layout('livewire.components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.payment-success-page');
    }
}
