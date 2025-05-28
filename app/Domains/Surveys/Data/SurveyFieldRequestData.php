<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Data;

use App\Domains\Surveys\Enums\SurveyFieldTypeEnum;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class SurveyFieldRequestData extends Data
{
    public function __construct(
        #[Exists('surveys', 'id')]
        public int $surveyId,

        #[Max(255)]
        public string $name,

        public SurveyFieldTypeEnum $type,
        public ?array $options = null,
    ) {
    }
}
