<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Data;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Models\Company;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
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
        public Company $company,
        public string $title,
        public ?string $description,
        public SurveyStatusEnum $status,

        public CarbonImmutable $startAt,
        public CarbonImmutable $endAt,

        public User $createdBy,
        public CarbonImmutable $createdAt,
        public CarbonImmutable $updatedAt,

        /** @var Collection<\App\Models\SurveyField> $fields */
        public Collection|Optional $fields,

        public ?User $deletedBy = null,
        public ?CarbonImmutable $deletedAt = null,
    ) {
    }
}
