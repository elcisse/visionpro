<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Tableau de bord'])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.dashboard.show');
    }
}
