<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Data;

use App\Domains\Surveys\Enums\SurveyFieldTypeEnum;
use App\Domains\Surveys\Models\Survey;
use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class SurveyFieldData extends Data
{
    public function __construct(
        public int $id,
        public Survey $survey,
        public string $name,
        public SurveyFieldTypeEnum $type,

        public User $createdBy,
        public CarbonImmutable $createdAt,
        public CarbonImmutable $updatedAt,

        public ?array $options = null,
        public ?User $deletedBy = null,
        public ?CarbonImmutable $deletedAt = null,
    ) {
    }
}
