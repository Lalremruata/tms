<?php

namespace App\Livewire;

use Livewire\Component;

class RedirectComponent extends Component
{
    public function render()
    {
        return view('livewire.redirect-component');
    }
    public function navigateToProfile()
    {
        // Navigate to a route without page refresh
        return redirect()->route('vskprofile');
    }


}
