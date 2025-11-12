<?php

namespace App\Livewire;

use Livewire\Component;

class DarkModeToggle extends Component
{
    public $darkMode = false;

    public function mount()
    {
        // Get dark mode preference from session or default to false
        $this->darkMode = session('darkMode', false);
    }

    public function toggle()
    {
        $this->darkMode = !$this->darkMode;
        session(['darkMode' => $this->darkMode]);
        
        // Dispatch browser event to update the theme
        $this->dispatch('dark-mode-toggled', darkMode: $this->darkMode);
    }

    public function render()
    {
        return view('livewire.dark-mode-toggle');
    }
}
