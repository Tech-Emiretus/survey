<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Controllers;

use App\Domains\Surveys\Actions\CreateSurveyAction;
use App\Domains\Surveys\Data\SurveyData;
use App\Domains\Surveys\Data\SurveyRequestData;
use App\Domains\Surveys\Models\Survey;
use App\Support\ApiResponse;
use Throwable;

class SurveyController
{
    public function __construct(
        private CreateSurveyAction $createSurveyAction,
    ) {
    }

    public function index(): ApiResponse
    {
        $surveys = Survey::with('company', 'createdBy', 'deletedBy')
            ->whereIn('company_id', request()->user()->companies->pluck('id')) // In production, this should be replaced with a join for better performance
            ->get();

        return ApiResponse::success(SurveyData::collect($surveys)->toArray());
    }

    public function store(SurveyRequestData $data): ApiResponse
    {
        try {
            $survey = $this->createSurveyAction->execute(request()->user(), $data);

            return ApiResponse::success(
                data: $survey->toArray(),
                message: 'Survey created successfully.',
                status: ApiResponse::HTTP_CREATED
            );
        } catch (Throwable $th) {
            return ApiResponse::error(
                message: $th->getMessage(),
                status: $th->getCode() ?: ApiResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(Survey $survey): ApiResponse
    {
        $survey->load('company', 'fields', 'createdBy', 'deletedBy');

        return ApiResponse::success(SurveyData::from($survey)->toArray());
    }
}
