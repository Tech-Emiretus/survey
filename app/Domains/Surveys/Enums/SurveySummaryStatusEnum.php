<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Enums;

enum SurveySummaryStatusEnum: string
{
    case Pending = 'pending';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';
}
