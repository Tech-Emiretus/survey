<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class SurveyRequestData extends Data
{
    public function __construct(
        #[Exists('companies', 'id')]
        public int $companyId,
        #[Max(255)]
        public string $title,
        public string|Optional $description,
        #[WithCast(DateTimeInterfaceCast::class)]
        #[BeforeOrEqual('end_at')]
        public CarbonImmutable $startAt,
        #[WithCast(DateTimeInterfaceCast::class)]
        #[AfterOrEqual('start_at')]
        public CarbonImmutable $endAt,
    ) {
    }
}
