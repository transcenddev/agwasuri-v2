<?php

namespace App\Livewire;

use App\Models\ModelMetadata;
use App\Models\WaterQualityData;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class ClassifyFish extends Component
{
    public $predictionResults = [];
    public $modelMetrics = [];
    public $error = null;
    public $waterQualityData = null;
    public $explanations = [];

    public ?int $userId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public bool $isAdminView = false;
    public bool $canPredict = false;

    public ?string $lastTrainedTimestamp = null;
    public bool $isTraining = false;

    const API_BASE_URL = 'https://api.agwasuri.app/predict'; // Using localhost for now
    const PREDICT_ENDPOINT = self::API_BASE_URL . '/predict';
    const TRAIN_ENDPOINT = self::API_BASE_URL . '/train_model';
    const MODEL_IDENTIFIER = 'fish_classifier'; // Define a constant for the model name

    #[On('user-selected')]
    public function updateUser($userId)
    {
        Log::info('ClassifyFish: User selected.', ['userId' => $userId]);
        $this->userId = (int) $userId;
        $this->resetState();
        $this->checkCanPredict();
    }

    #[On('date-range-updated')]
    public function updateDates($startDate, $endDate)
    {
        Log::info('ClassifyFish: Date range updated.', ['start' => $startDate, 'end' => $endDate]);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->resetState();
        $this->checkCanPredict();
    }

    private function resetState()
    {
        $this->predictionResults = [];
        $this->modelMetrics = [];
        $this->explanations = [];
        $this->waterQualityData = null;
        $this->error = null;
    }

    private function checkCanPredict()
    {
        $this->canPredict = !is_null($this->userId);
        Log::info('ClassifyFish: CheckCanPredict.', ['userId' => $this->userId, 'canPredict' => $this->canPredict]);
    }

    public function mount()
    {
        Log::info('ClassifyFish: Mounting component.');
        $loggedInUser = Auth::user();

        if (!$loggedInUser || !$loggedInUser->getKey()) {
            Log::error('ClassifyFish: Mount - Failed to get authenticated user or user ID is missing.');
            $this->canPredict = false;
            return;
        }

        $this->userId = $loggedInUser->getKey();
        $this->isAdminView = $loggedInUser->isAdmin();

        if ($this->isAdminView) {
            $this->userId = null;
            Log::info('ClassifyFish: Mount - Admin view, awaiting user selection.');
            $this->fetchLastTrainedTimestampFromDb();
        } else {
            Log::info('ClassifyFish: Mount - Non-admin view.', ['userId' => $this->userId]);
        }
        $this->checkCanPredict();
    }

    public function fetchLastTrainedTimestampFromDb()
    {
        if (!$this->isAdminView) return;

        Log::info('ClassifyFish: Fetching last trained timestamp from DB.');
        try {
            $metadata = ModelMetadata::where('model_name', self::MODEL_IDENTIFIER)->first();

            if ($metadata && $metadata->last_trained_at) {
                $this->lastTrainedTimestamp = $metadata->last_trained_at->format('Y-m-d H:i:s');
                Log::info('ClassifyFish: Last trained timestamp fetched from DB.', ['timestamp' => $this->lastTrainedTimestamp]);
            } else {
                Log::warning('ClassifyFish: Timestamp not found in DB for model.', ['model_name' => self::MODEL_IDENTIFIER]);
                $this->lastTrainedTimestamp = 'N/A';
            }
        } catch (\Exception $e) {
            Log::error('ClassifyFish: Exception fetching last trained timestamp from DB.', ['message' => $e->getMessage()]);
            $this->lastTrainedTimestamp = 'Error fetching date';
        }
    }

    public function trainModel()
    {
        if (!$this->isAdminView) {
            session()->flash('train_error', 'You do not have permission to train the model.');
            return;
        }

        if ($this->isTraining) {
            return;
        }

        Log::info('ClassifyFish: Initiating model training request.');
        $this->isTraining = true;
        session()->forget(['train_message', 'train_error']);

        try {
            $response = Http::post(self::TRAIN_ENDPOINT);

            if ($response->successful()) {
                $responseData = $response->json();
                $message = $responseData['message'] ?? 'Model training completed successfully.';
                session()->flash('train_message', $message);
                Log::info('ClassifyFish: Model training request successful.', ['response' => $responseData]);

                if (isset($responseData['timestamp'])) {
                    $timestampString = $responseData['timestamp'];
                    try {
                        $parsedTimestamp = Carbon::parse($timestampString);

                        $metadata = ModelMetadata::updateOrCreate(
                            ['model_name' => self::MODEL_IDENTIFIER],
                            ['last_trained_at' => $parsedTimestamp]
                        );

                        $this->lastTrainedTimestamp = $metadata->last_trained_at->format('Y-m-d H:i:s');
                        Log::info('ClassifyFish: Saved/Updated last trained timestamp in DB.', ['timestamp' => $this->lastTrainedTimestamp]);

                    } catch (\Exception $parseOrSaveError) {
                        Log::error('ClassifyFish: Failed to parse or save timestamp from train response.', [
                            'timestamp_string' => $timestampString,
                            'error' => $parseOrSaveError->getMessage()
                        ]);
                        session()->flash('train_error', 'Training successful, but failed to record timestamp locally.');
                        $this->fetchLastTrainedTimestampFromDb();
                    }
                } else {
                    Log::warning('ClassifyFish: Timestamp missing in successful train response. Cannot update DB record.', ['response' => $responseData]);
                    session()->flash('train_error', 'Training successful, but timestamp was missing in response.');
                }

            } else {
                $errorDetail = $response->json('detail') ?? 'Unknown error occurred during training.';
                $errorMessage = 'Failed to train model (Status: ' . $response->status() . ') - ' . $errorDetail;
                session()->flash('train_error', $errorMessage);
                Log::error('ClassifyFish: Model training request failed.', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $errorMessage = 'An unexpected error occurred while contacting the training service: ' . $e->getMessage();
            session()->flash('train_error', $errorMessage);
            Log::error('ClassifyFish: Exception during trainModel request.', ['message' => $e->getMessage()]);
        } finally {
            $this->isTraining = false;
        }
    }

    public function predictFishType()
    {
        if (!$this->canPredict || is_null($this->userId)) {
            $this->error = 'Please select a user to perform prediction.';
            Log::warning('ClassifyFish: Predict attempt without user selected.', ['userId' => $this->userId]);
            return;
        }

        Log::info('ClassifyFish: Starting prediction.', ['userId' => $this->userId, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);
        $this->resetState();

        try {
            $query = WaterQualityData::where('user_id', $this->userId);

            if ($this->startDate && $this->endDate) {
                $query->whereBetween('recorded_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            } elseif ($this->startDate) {
                $query->where('recorded_at', '>=', $this->startDate . ' 00:00:00');
            } elseif ($this->endDate) {
                $query->where('recorded_at', '<=', $this->endDate . ' 23:59:59');
            }

            $averageData = $query->selectRaw(
                'AVG(temperature) as temperature,
                AVG(salinity) as salinity,
                AVG(dissolved_oxygen) as dissolved_oxygen,
                AVG(ph_level) as ph_level'
            )->first();

            if (is_null($averageData) || is_null($averageData->temperature)) {
                $this->error = 'No water quality data found for the selected user' . ($this->startDate || $this->endDate ? ' in the specified date range.' : '.');
                Log::warning('ClassifyFish: No data found for averaging.', ['userId' => $this->userId, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);
                return;
            }

            $averageDataArray = $averageData->toArray();
            $this->waterQualityData = [
                'temperature' => round($averageDataArray['temperature'], 2),
                'dissolved_oxygen' => round($averageDataArray['dissolved_oxygen'], 2),
                'salinity' => round($averageDataArray['salinity'], 2),
                'ph_level' => round($averageDataArray['ph_level'], 2),
            ];

            $response = Http::post(self::PREDICT_ENDPOINT, [
                'optimal_temperature_C' => (float) $this->waterQualityData['temperature'],
                'optimal_dissolved_oxygen_mgL' => (float) $this->waterQualityData['dissolved_oxygen'],
                'optimal_salinity_ppt' => (float) $this->waterQualityData['salinity'],
                'optimal_ph_value' => (float) $this->waterQualityData['ph_level']
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('ClassifyFish: API Prediction Response:', $data);
                $this->predictionResults = $data['predictions'];
                $this->modelMetrics = [
                    'accuracy' => $data['model_accuracy_percentage'],
                    'precision' => $data['precision_percentage'],
                    'recall' => $data['recall_percentage'],
                    'f1_score' => $data['f1_score_percentage']
                ];
                $this->generateExplanations();
                $this->error = null;
            } else {
                Log::error('ClassifyFish: Prediction API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                $this->error = 'Failed to get prediction from API (Status: ' . $response->status() . ')';
                $this->resetState();
            }
        } catch (\Exception $e) {
            Log::error('ClassifyFish: Exception during prediction.', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->error = 'An unexpected error occurred during prediction: ' . $e->getMessage();
            $this->resetState();
        }
    }

    private function generateExplanations()
    {
        if (is_null($this->waterQualityData)) {
            return;
        }

        $dateRangeText = '';
        if($this->startDate && $this->endDate) {
            $dateRangeText = " between {$this->startDate} and {$this->endDate}";
        } elseif ($this->startDate) {
            $dateRangeText = " from {$this->startDate} onwards";
        } elseif ($this->endDate) {
            $dateRangeText = " up to {$this->endDate}";
        } else {
            $dateRangeText = " across all available records";
        }

        $this->explanations['predictions'] = "Based on the average water quality measurements for the selected user{$dateRangeText}:
            Temperature: {$this->waterQualityData['temperature']}°C,
            Dissolved Oxygen: {$this->waterQualityData['dissolved_oxygen']} mg/L,
            Salinity: {$this->waterQualityData['salinity']} ppt,
            pH: {$this->waterQualityData['ph_level']}.";

        if (!empty($this->predictionResults)) {
            $topPrediction = $this->predictionResults[0];
            $this->explanations['top_prediction'] = "The model predicts that
                {$topPrediction['species_name']} is the most suitable species with
                {$topPrediction['confidence_percentage']}% confidence.";

            if ($topPrediction['confidence_percentage'] >= 90) {
                $this->explanations['confidence_context'] = "This is a very high confidence prediction.";
            } elseif ($topPrediction['confidence_percentage'] >= 70) {
                $this->explanations['confidence_context'] = "This is a moderately high confidence prediction.";
            } else {
                $this->explanations['confidence_context'] = "This prediction has lower confidence. Consider monitoring water quality parameters closely.";
            }
        } else {
            $this->explanations['top_prediction'] = "No prediction could be made.";
            $this->explanations['confidence_context'] = '';
        }

        if (!empty($this->modelMetrics)) {
            $this->explanations['metrics'] = [
                'accuracy' => "The model correctly identifies fish species {$this->modelMetrics['accuracy']}% of the time.",
                'precision' => "When the model predicts a specific fish species, it is correct {$this->modelMetrics['precision']}% of the time.",
                'recall' => "The model successfully identifies {$this->modelMetrics['recall']}% of actual occurrences of each fish species.",
                'f1_score' => "The overall balanced performance score of the model is {$this->modelMetrics['f1_score']}%."
            ];
        } else {
            $this->explanations['metrics'] = [];
        }
    }

    public function render()
    {
        return view('livewire.classify-fish', [
            'predictionResults' => $this->predictionResults,
            'modelMetrics' => $this->modelMetrics,
            'error' => $this->error,
            'explanations' => $this->explanations,
            'waterQualityData' => $this->waterQualityData,
            'canPredict' => $this->canPredict,
            'isTraining' => $this->isTraining,
            'lastTrainedTimestamp' => $this->lastTrainedTimestamp
        ]);
    }
}
