<?php

namespace App\Livewire;

use App\Models\WaterQualityData;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportPagination\WithoutUrlPagination;
use Livewire\WithPagination;

class WaterQualityDataTable extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $paginate_num;

    #[On('echo:water-quality-data-created,WaterQualityCreated')]
    public function refreshTable()
    {
        Log::info("Refreshing RefreshWaterQualityTable");
    }

    public function mount()
    {
        $this->paginate_num = Route::currentRouteName() === 'historical-data' ? 50 : 10;
    }

    public function render()
    {
        $water_quality_data = WaterQualityData::latest()->paginate(perPage: $this->paginate_num);
        return \view('livewire.water-quality-data-table', compact('water_quality_data'));
    }
}
