<?php

namespace App\Livewire;

use Livewire\Component;

class MapComponent extends Component
{
    public $employeeCoordinates = [];

    // Pass initial coordinates or fetch from the database
    public function mount($coordinates)
    {
        $this->employeeCoordinates = $coordinates;
    }

    public function render()
    {
        return view('livewire.map-component');
    }
}
