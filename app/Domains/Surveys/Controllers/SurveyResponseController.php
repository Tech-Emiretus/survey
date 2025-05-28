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
        $responses = $survey->responses()->get();
        return ApiResponse::success($responses->toArray());
    }

    public function show(SurveyResponse $response): ApiResponse
    {
        $response->load('fieldResponses');
        return ApiResponse::success($response->toArray());
    }
}
