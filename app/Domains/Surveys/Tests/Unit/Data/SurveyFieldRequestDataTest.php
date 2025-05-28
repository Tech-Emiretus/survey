<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Unit\Data;

use App\Domains\Surveys\Data\SurveyFieldRequestData;
use App\Domains\Surveys\Enums\SurveyFieldTypeEnum;
use Illuminate\Validation\ValidationException;

describe('SurveyFieldRequestData', function () {
    it('validates required fields', function (array $data, string $exception, string $message) {
        expect(fn() => SurveyFieldRequestData::from($data))
            ->toThrow($exception, $message);
    })->with('validation_data_set');

    it('creates a valid SurveyFieldRequestData instance', function () {
        $data = [
            'name' => 'Sample Field',
            'type' => 'text',
            'options' => [],
        ];

        $surveyFieldRequestData = SurveyFieldRequestData::from($data);

        expect($surveyFieldRequestData)
            ->name->toBe($data['name'])
            ->type->toBe(SurveyFieldTypeEnum::Text)
            ->options->toBe($data['options']);
    });
});

dataset('validation_data_set', function (): array {
    $data = [
        'name' => 'Sample Field',
        'type' => 'text',
        'options' => [],
    ];

    return [
        'when all fields are empty' => [
            'data' => [],
            'exception' => ValidationException::class,
            'message' => 'The name field is required. (and 1 more error)',
        ],
        'when name is missing' => [
            'data' => [...$data, 'name' => null],
            'exception' => ValidationException::class,
            'message' => 'The name field is required.',
        ],
        'when name greater than 255 characters' => [
            'data' => [...$data, 'name' => str()->random(300)],
            'exception' => ValidationException::class,
            'message' => 'The name field must not be greater than 255 characters.',
        ],
        'when type is missing' => [
            'data' => [...$data, 'type' => null],
            'exception' => ValidationException::class,
            'message' => 'The type field is required.',
        ],
        'when type is invalid' => [
            'data' => [...$data, 'type' => 'invalid-type'],
            'exception' => ValidationException::class,
            'message' => 'The selected type is invalid.',
        ],
    ];
});
