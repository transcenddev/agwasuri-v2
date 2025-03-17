<?php

namespace App\Livewire;

use App\Models\WaterQualityData;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class WaterQualityChart extends Component
{
    public $water_quality_data;


    #[On('echo:water-quality-data-created,WaterQualityCreated')]
    public function mount()
    {
        $this->water_quality_data = WaterQualityData::all();
    }

    public function render()
    {
        $water_quality_data = $this->water_quality_data;

        $lineChartModel = $water_quality_data->reduce(function ($lineChartModel, $water_quality_data)
        {
            $recorded_at = Carbon::parse($water_quality_data->recorded_at)->format('y|m|d H:i:s');

            $lineChartModel = $lineChartModel
                ->addSeriesPoint('Temperature', $recorded_at, $water_quality_data->temperature)
                ->addSeriesPoint('pH Level', $recorded_at, $water_quality_data->ph_level)
                ->addSeriesPoint('Dissolved Oxygen', $recorded_at, $water_quality_data->dissolved_oxygen)
                ->addSeriesPoint('Salinity', $recorded_at, $water_quality_data->salinity)
                ;

            return $lineChartModel;
        },
        (new LineChartModel)
            ->setTitle('Water Quality Data')
            ->multiLine()
        );

        return \view('livewire.water-quality-chart')->with([
            'lineChartModel' => $lineChartModel,
        ]);
    }
}
