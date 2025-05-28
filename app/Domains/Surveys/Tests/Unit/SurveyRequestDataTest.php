<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Unit;

use App\Domains\Surveys\Data\SurveyRequestData;
use App\Models\Company;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Exceptions\CannotCastDate;

describe('SurveyRequestData', function () {
    it('validates required fields', function (array $data, string $exception, string $message) {
        if (!empty($data['company_id'])) {
            Company::factory()->create(['id' => $data['company_id']]);
        }

        expect(fn () => SurveyRequestData::from($data))
            ->toThrow($exception, $message);
    })->with('validation_data_set');

    it('creates a valid SurveyRequestData instance', function () {
        $company = Company::factory()->create();
        $data = [
            'company_id' => $company->id,
            'title' => 'Sample Survey',
            'description' => 'This is a sample survey description.',
            'start_at' => '2025-05-27 00:00:00',
            'end_at' => '2025-06-03 00:00:00',
        ];

        $surveyRequestData = SurveyRequestData::from($data);

        expect($surveyRequestData)
            ->companyId->toBe($data['company_id'])
            ->title->toBe($data['title'])
            ->description->toBe($data['description'])
            ->startAt->toEqual($data['start_at'])
            ->endAt->toEqual($data['end_at']);
    });
});

dataset('validation_data_set', function (): array {
    $data = [
        'company_id' => 1,
        'title' => 'Sample Survey',
        'description' => 'This is a sample survey description.',
        'start_at' => now()->toImmutable(),
        'end_at' => now()->addDays(7)->toImmutable(),
    ];

    return [
        'when all fields are empty' => [
            'data' => [],
            'exception' => ValidationException::class,
            'message' => 'The company id field is required. (and 3 more errors)',
        ],
        'when company_id is missing' => [
            'data' => [...$data, 'company_id' => null],
            'exception' => ValidationException::class,
            'message' => 'The company id field is required.',
        ],
        'when title is missing' => [
            'data' => [...$data, 'title' => null],
            'exception' => ValidationException::class,
            'message' => 'The title field is required.',
        ],
        'when start_at is missing' => [
            'data' => [...$data, 'start_at' => null],
            'exception' => ValidationException::class,
            'message' => 'The start at field is required.',
        ],
        'when start_at is invalid' => [
            'data' => [...$data, 'start_at' => 'invalid-date'],
            'exception' => CannotCastDate::class,
            'message' => 'Could not cast date `invalid-date` into a `Carbon\CarbonImmutable`',
        ],
        'when end_at is missing' => [
            'data' => [...$data, 'end_at' => null],
            'exception' => ValidationException::class,
            'message' => 'The start at field must be a date before or equal to end at. (and 1 more error)',
        ],
        'when end_at is invalid' => [
            'data' => [...$data, 'end_at' => 'invalid-date'],
            'exception' => ValidationException::class,
            'message' => 'The start at field must be a date before or equal to end at. (and 1 more error)',
        ],
        'when end_at is less than start_at' => [
            'data' => [...$data, 'start_at' => now(), 'end_at' => now()->subDays(1)],
            'exception' => ValidationException::class,
            'message' => 'The start at field must be a date before or equal to end at.',
        ],
    ];
});
