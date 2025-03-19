<?php

namespace App\Livewire;

use App\Models\WaterQualityData;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class WaterQualityChart extends Component
{
    public $water_quality_data;


    #[On('echo:water-quality-data-created,WaterQualityCreated')]
    public function mount()
    {
        $this->water_quality_data = WaterQualityData::where('user_id', Auth::id())->get();
    }

    public function render()
    {
        $water_quality_data = $this->water_quality_data;

        if ($water_quality_data->isEmpty()) {
            $lineChartModel = null;
        } else {
            $lineChartModel = $water_quality_data->reduce(function ($lineChartModel, $data) {
                $recorded_at = Carbon::parse($data->recorded_at)->format('y|m|d H:i:s');

                return $lineChartModel
                    ->addSeriesPoint('Temperature', $recorded_at, $data->temperature)
                    ->addSeriesPoint('pH Level', $recorded_at, $data->ph_level)
                    ->addSeriesPoint('Dissolved Oxygen', $recorded_at, $data->dissolved_oxygen)
                    ->addSeriesPoint('Salinity', $recorded_at, $data->salinity);
            }, (new LineChartModel)
                ->setTitle('Water Quality Data')
                ->multiLine()
            );
        }

        return view('livewire.water-quality-chart', [
            'lineChartModel' => $lineChartModel,
        ]);
    }
}
