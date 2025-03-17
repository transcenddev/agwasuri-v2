<?php

namespace App\Livewire;

use App\Models\WaterQualityData;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class WaterQualityCard extends Component
{

    #[Computed]
    public $temperature;
    #[Computed]
    public $dissolved_oxygen;
    #[Computed]
    public $salinity;
    #[Computed]
    public $ph_level;

    #[On('echo:water-quality-data-created,WaterQualityCreated')]
    public function mount()
    {
        $waterQualityData = WaterQualityData::latest()->first();

        if ($waterQualityData) {
            $this->temperature = $waterQualityData->temperature;
            $this->salinity = $waterQualityData->salinity;
            $this->ph_level = $waterQualityData->ph_level;
            $this->dissolved_oxygen = $waterQualityData->dissolved_oxygen;
        }
    }

    public function render()
    {
        return view('livewire.water-quality-card');
    }
}
