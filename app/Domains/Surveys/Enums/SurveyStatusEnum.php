<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Enums;

enum SurveyStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
