<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Routes;

use App\Domains\Surveys\Controllers\SurveyController;
use App\Domains\Surveys\Controllers\SurveyFieldController;
use App\Domains\Surveys\Controllers\SurveyParticipationController;
use App\Domains\Surveys\Controllers\SurveyResponseController;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyField;
use App\Domains\Surveys\Models\SurveyResponse;
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
            ->can('view', ['survey']);

        Route::put('/{survey}', [SurveyController::class, 'update'])
            ->name('domains.surveys.update')
            ->can('update', ['survey']);

        Route::delete('/{survey}', [SurveyController::class, 'destroy'])
            ->name('domains.surveys.destroy')
            ->can('delete', ['survey']);

        Route::post('/{survey}/fields', [SurveyFieldController::class, 'store'])
            ->name('domains.surveys.fields.store')
            ->can('create', [SurveyField::class, 'survey']);

        Route::delete('/{survey}/fields/{field}', [SurveyFieldController::class, 'destroy'])
            ->name('domains.surveys.fields.destroy')
            ->can('delete', ['field', 'survey']);

        Route::get('/{survey}/responses', [SurveyResponseController::class, 'index'])
            ->name('domains.surveys.responses.index')
            ->can('viewAny', [SurveyResponse::class, 'survey']);

        Route::get('/{survey}/responses/{response}', [SurveyResponseController::class, 'show'])
            ->name('domains.surveys.responses.show')
            ->can('view', ['response', 'survey']);
    });
});

Route::prefix('/participate')->group(function (): void {
    Route::get('/{survey:public_id}', [SurveyParticipationController::class, 'show'])
        ->name('domains.participate.show');

    Route::post('/{survey:public_id}', [SurveyParticipationController::class, 'store'])
        ->name('domains.participate.store');
});
