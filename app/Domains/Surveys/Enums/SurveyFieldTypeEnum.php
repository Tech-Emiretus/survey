<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Enums;

enum SurveyFieldTypeEnum: string
{
    case Text = 'text';
    case TextArea = 'textarea';
    case Radio = 'radio';
}
