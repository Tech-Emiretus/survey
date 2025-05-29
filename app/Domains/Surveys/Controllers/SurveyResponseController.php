<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Controllers;

use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyResponse;
use App\Support\ApiResponse;

class SurveyResponseController
{
    public function index(Survey $survey): ApiResponse
    {
        $responses = $survey->responses()->orderBy('id', 'desc')->get();
        return ApiResponse::success($responses->toArray());
    }

    public function show(Survey $survey, SurveyResponse $response): ApiResponse
    {
        $response->load('fieldResponses.surveyField');

        return ApiResponse::success([...$response->toArray(), 'survey' => $survey->toArray()]);
    }
}
