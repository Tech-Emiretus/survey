<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Controllers;

use App\Domains\Surveys\Actions\CreateSurveyAction;
use App\Domains\Surveys\Data\SurveyRequestData;
use App\Support\ApiResponse;
use Throwable;

class SurveyController
{
    public function __construct(
        private CreateSurveyAction $createSurveyAction,
    ) {
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
                status: ApiResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
