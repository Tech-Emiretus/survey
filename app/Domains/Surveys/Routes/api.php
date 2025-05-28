<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Routes;

use App\Domains\Surveys\Controllers\SurveyController;
use App\Domains\Surveys\Controllers\SurveyFieldController;
use App\Domains\Surveys\Controllers\SurveyParticipationController;
use App\Domains\Surveys\Models\Survey;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::prefix('/surveys')->group(function (): void {
        Route::get('/', [SurveyController::class, 'index'])
            ->name('domains.surveys.index')
            ->can('viewAny', Survey::class);

        Route::post('/', [SurveyController::class, 'store'])
            ->name('domains.surveys.store')
            ->can('create', Survey::class);

        Route::get('/{survey}', [SurveyController::class, 'show'])
            ->name('domains.surveys.show')
            ->can('view,survey');

        Route::post('/{survey}/fields', [SurveyFieldController::class, 'store'])
            ->name('domains.surveys.fields.store');
    });
});

Route::prefix('/participate')->group(function (): void {
    Route::get('/{survey:public_id}', [SurveyParticipationController::class, 'show'])
        ->name('domains.participate.show');

    Route::post('/{survey:public_id}', [SurveyParticipationController::class, 'store'])
        ->name('domains.participate.store');
});
