<?php

use App\Http\Controllers\Api\V1\AiModelController;
use App\Http\Controllers\Api\V1\AutomationActionController;
use App\Http\Controllers\Api\V1\AutomationFlowController;
use App\Http\Controllers\Api\V1\AutomationJobController;
use App\Http\Controllers\Api\V1\BenchmarkResultController;
use App\Http\Controllers\Api\V1\BenchmarkSessionController;
use App\Http\Controllers\Api\V1\CleanedOutputController;
use App\Http\Controllers\Api\V1\DatasetController;
use App\Http\Controllers\Api\V1\DatasetItemController;
use App\Http\Controllers\Api\V1\FeedbackLogController;
use App\Http\Controllers\Api\V1\HumanGroundTruthController;
use App\Http\Controllers\Api\V1\MasterPromptController;
use App\Http\Controllers\Api\V1\ModelEvaluationController;
use App\Http\Controllers\Api\V1\ModelReleaseController;
use App\Http\Controllers\Api\V1\OutputTypeController;
use App\Http\Controllers\Api\V1\ProcessedOutputController;
use App\Http\Controllers\Api\V1\ReleaseReportController;
use App\Http\Controllers\Api\V1\ServiceCallLogController;
use App\Http\Controllers\Api\V1\ServiceRegistryController;
use App\Http\Controllers\Api\V1\SourceFileController;
use App\Http\Controllers\Api\V1\TrainedModelController;
use App\Http\Controllers\Api\V1\TrainingJobController;
use App\Http\Controllers\Api\V1\VectorCollectionController;
use App\Http\Controllers\Api\V1\VectorCollectionItemController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::apiResource('files', SourceFileController::class);
    Route::apiResource('output-types', OutputTypeController::class)->parameters(['output-types' => 'outputType']);
    Route::apiResource('automation-actions', AutomationActionController::class)->parameters(['automation-actions' => 'automationAction']);
    Route::apiResource('prompts', MasterPromptController::class);
    Route::apiResource('models', AiModelController::class);
    Route::apiResource('automation-flows', AutomationFlowController::class)->parameters(['automation-flows' => 'automationFlow']);
    Route::apiResource('services', ServiceRegistryController::class)->parameters(['services' => 'service']);
    Route::apiResource('jobs', AutomationJobController::class);
    Route::apiResource('outputs', ProcessedOutputController::class);
    Route::apiResource('cleaned-outputs', CleanedOutputController::class)->parameters(['cleaned-outputs' => 'cleanedOutput']);
    Route::apiResource('ground-truth', HumanGroundTruthController::class)->parameters(['ground-truth' => 'groundTruth']);
    Route::apiResource('benchmark-sessions', BenchmarkSessionController::class)->parameters(['benchmark-sessions' => 'benchmarkSession']);
    Route::apiResource('benchmark-results', BenchmarkResultController::class)->parameters(['benchmark-results' => 'benchmarkResult']);
    Route::apiResource('datasets', DatasetController::class);
    Route::apiResource('dataset-items', DatasetItemController::class)->parameters(['dataset-items' => 'datasetItem']);
    Route::apiResource('training-jobs', TrainingJobController::class)->parameters(['training-jobs' => 'trainingJob']);
    Route::apiResource('trained-models', TrainedModelController::class)->parameters(['trained-models' => 'trainedModel']);
    Route::apiResource('model-evaluations', ModelEvaluationController::class)->parameters(['model-evaluations' => 'modelEvaluation']);
    Route::apiResource('model-releases', ModelReleaseController::class)->parameters(['model-releases' => 'modelRelease']);
    Route::apiResource('release-reports', ReleaseReportController::class)->parameters(['release-reports' => 'releaseReport']);
    Route::apiResource('vector-collections', VectorCollectionController::class)->parameters(['vector-collections' => 'vectorCollection']);
    Route::apiResource('vector-collections/{vectorCollection}/items', VectorCollectionItemController::class)
        ->parameters(['items' => 'vectorCollectionItem']);
    Route::apiResource('feedbacks', FeedbackLogController::class);
    Route::apiResource('service-call-logs', ServiceCallLogController::class)->parameters(['service-call-logs' => 'serviceCallLog']);
});
