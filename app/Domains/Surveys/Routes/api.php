<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Routes;

use App\Domains\Surveys\Controllers\SurveyController;
use App\Domains\Surveys\Controllers\SurveyFieldController;
use App\Domains\Surveys\Models\Survey;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/surveys', 'middleware' => ['auth']], function (): void {
    Route::post('/', [SurveyController::class, 'store'])
        ->name('domains.surveys.store')
        ->can('create', Survey::class);

    Route::post('/{survey}/fields', [SurveyFieldController::class, 'store'])
        ->name('domains.surveys.fields.store');
});
