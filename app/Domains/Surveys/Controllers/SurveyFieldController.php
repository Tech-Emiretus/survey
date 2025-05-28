<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Controllers;

use App\Domains\Surveys\Actions\CreateSurveyFieldAction;
use App\Domains\Surveys\Data\SurveyFieldRequestData;
use App\Domains\Surveys\Models\Survey;
use App\Support\ApiResponse;
use Throwable;

class SurveyFieldController
{
    public function __construct(
        private CreateSurveyFieldAction $createSurveyFieldAction,
    ) {
    }

    public function store(Survey $survey, SurveyFieldRequestData $data): ApiResponse
    {
        try {
            $field = $this->createSurveyFieldAction->execute(request()->user(), $survey, $data);

            return ApiResponse::success(
                data: $field->toArray(),
                message: 'Survey field created successfully.',
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
