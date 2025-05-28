<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Data;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class SurveyData extends Data
{
    public function __construct(
        public int $id,
        public string $publicId,
        public int $companyId,
        public string $title,
        public string|Optional $description,
        public SurveyStatusEnum $status,

        public CarbonImmutable $startAt,
        public CarbonImmutable $endAt,
        public int $createdBy,
        public int|Optional $deletedBy,

        public CarbonImmutable $createdAt,
        public CarbonImmutable $updatedAt,
        public CarbonImmutable|Optional $deletedAt,
    ) {
    }
}
