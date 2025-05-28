<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Controllers;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveySummary;
use App\Support\ApiResponse;

class SurveySummaryController
{
    public function index(Survey $survey): ApiResponse
    {
        $summaries = $survey->summaries()->get();
        return ApiResponse::success($summaries->toArray());
    }

    public function store(Survey $survey): ApiResponse
    {
        $summary = $survey->summaries()->create([
            'status' => SurveySummaryStatusEnum::Pending,
            'created_by' => request()->user()->id,
        ]);

        return ApiResponse::success($summary->toArray(), 'Survey summary created.', ApiResponse::HTTP_CREATED);
    }

    public function show(Survey $survey, SurveySummary $summary): ApiResponse
    {
        return ApiResponse::success([...$summary->toArray(), 'survey' => $survey->toArray()]);
    }
}
