<?php

namespace App\Livewire;

use App\Models\WaterQualityData;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class ClassifyFish extends Component
{
    public $predictionResults = [];
    public $modelMetrics = [];
    public $error = null;
    public $waterQualityData = null;
    public $explanations = [];

    public function predictFishType()
    {
        try {
            // Get averages of all water quality data
            $averageData = WaterQualityData::selectRaw(
                'AVG(temperature) as temperature,
                AVG(salinity) as salinity,
                AVG(dissolved_oxygen) as dissolved_oxygen,
                AVG(ph_level) as ph_level'
            )->first()
            ->toArray();


            $this->waterQualityData = [
                'temperature' => round($averageData['temperature'], 2),
                'dissolved_oxygen' => round($averageData['dissolved_oxygen'], 2),
                'salinity' => round($averageData['salinity'], 2),
                'ph_level' => round($averageData['ph_level'], 2),
            ];

            // Make API request to FastAPI endpoint
            $response = Http::post('https://api.lokodata.site/predict', [
                'optimal_temperature_C' => (float) $averageData['temperature'],
                'optimal_dissolved_oxygen_mgL' => (float) $averageData['dissolved_oxygen'],
                'optimal_salinity_ppt' => (float) $averageData['salinity'],
                'optimal_ph_value' => (float) $averageData['ph_level']
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store predictions
                $this->predictionResults = $data['predictions'];

                // Store model metrics
                $this->modelMetrics = [
                    'accuracy' => $data['model_accuracy_percentage'],
                    'precision' => $data['precision_percentage'],
                    'recall' => $data['recall_percentage'],
                    'f1_score' => $data['f1_score_percentage']
                ];

                // Generate explanations
                $this->generateExplanations();

                $this->error = null;
            } else {
                $this->error = 'Failed to get prediction from API';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }
    }

    private function generateExplanations()
    {
        // Explanation for predictions
        $this->explanations['predictions'] = "Based on the average water quality measurements of your fishpond:
            Temperature: {$this->waterQualityData['temperature']}°C,
            Dissolved Oxygen: {$this->waterQualityData['dissolved_oxygen']} mg/L,
            Salinity: {$this->waterQualityData['salinity']} ppt,
            pH: {$this->waterQualityData['ph_level']}.";

        // Get the highest confidence prediction
        $topPrediction = $this->predictionResults[0];
        $this->explanations['top_prediction'] = "The model predicts that
            {$topPrediction['species_name']} is the most suitable species with
            {$topPrediction['confidence_percentage']}% confidence.";

        // Explanation for model metrics
        $this->explanations['metrics'] = [
            'accuracy' => "The model correctly identifies fish species {$this->modelMetrics['accuracy']}% of the time.",
            'precision' => "When the model predicts a specific fish species, it is correct {$this->modelMetrics['precision']}% of the time.",
            'recall' => "The model successfully identifies {$this->modelMetrics['recall']}% of actual occurrences of each fish species.",
            'f1_score' => "The overall balanced performance score of the model is {$this->modelMetrics['f1_score']}%."
        ];

        // Additional context based on confidence levels
        if ($topPrediction['confidence_percentage'] >= 90) {
            $this->explanations['confidence_context'] = "This is a very high confidence prediction.";
        } elseif ($topPrediction['confidence_percentage'] >= 70) {
            $this->explanations['confidence_context'] = "This is a moderately high confidence prediction.";
        } else {
            $this->explanations['confidence_context'] = "This prediction has lower confidence. Consider monitoring water quality parameters closely.";
        }
    }

    public function render()
    {
        return view('livewire.classify-fish', [
            'predictionResults' => $this->predictionResults,
            'modelMetrics' => $this->modelMetrics,
            'error' => $this->error,
            'explanations' => $this->explanations,
            'waterQualityData' => $this->waterQualityData
        ]);
    }
}
