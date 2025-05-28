<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Controllers;

use App\Domains\Surveys\Actions\CreateSurveyResponseAction;
use App\Domains\Surveys\Data\SurveyData;
use App\Domains\Surveys\Data\SurveyResponseRequestData;
use App\Domains\Surveys\Models\Survey;
use App\Support\ApiResponse;
use Throwable;

class SurveyParticipationController
{
    public function __construct(
        private CreateSurveyResponseAction $createSurveyResponseAction,
    ) {
    }

    public function show(Survey $survey): ApiResponse
    {
        if (!$survey->isActive()) {
            return ApiResponse::error(
                message: 'This survey is not active.',
                status: ApiResponse::HTTP_BAD_REQUEST
            );
        }

        $survey->load('company', 'fields', 'createdBy', 'deletedBy');

        return ApiResponse::success(SurveyData::from($survey)->toArray());
    }

    public function store(Survey $survey, SurveyResponseRequestData $data): ApiResponse
    {
        try {
            $survey = $this->createSurveyResponseAction->execute($survey, $data);

            return ApiResponse::success(
                message: 'Survey response created successfully.',
                status: ApiResponse::HTTP_CREATED
            );
        } catch (Throwable $th) {
            return ApiResponse::error(
                message: $th->getMessage(),
                status: $th->getCode() ?: ApiResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
