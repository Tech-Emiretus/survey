<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Unit\Data;

use App\Domains\Surveys\Data\SurveyResponseRequestData;
use App\Domains\Surveys\Models\SurveyField;
use Illuminate\Validation\ValidationException;

describe('SurveyResponseRequestData', function () {
    it('validates required fields', function (array $data, string $exception, string $message) {
        if (!empty($data['responses']) && is_array($data['responses'])) {
            foreach ($data['responses'] as $response) {
                if (!empty($response['survey_field_id'])) {
                    SurveyField::factory()->create(['id' => $response['survey_field_id']]);
                }
            }
        }

        expect(fn () => SurveyResponseRequestData::from($data))
            ->toThrow($exception, $message);
    })->with('validation_data_set');

    it('creates a valid SurveyResponseRequestData instance', function () {
        $field = SurveyField::factory()->create();
        $data = [
            'respondent_name' => 'John Doe',
            'respondent_email' => 'john@example.com',
            'responses' => [
                [
                    'survey_field_id' => $field->id,
                    'response' => 'Blue',
                ],
            ],
        ];

        $dto = SurveyResponseRequestData::from($data);

        expect($dto)
            ->respondentName->toBe($data['respondent_name'])
            ->respondentEmail->toBe($data['respondent_email'])
            ->responses->toBe($data['responses']);
    });
});

dataset('validation_data_set', function (): array {
    $data = [
        'respondent_name' => 'Jane Doe',
        'respondent_email' => 'jane@example.com',
        'responses' => [
            [
                'survey_field_id' => 1,
                'response' => 'Red',
            ],
        ],
    ];

    return [
        'when all fields are empty' => [
            'data' => [],
            'exception' => ValidationException::class,
            'message' => 'The respondent name field is required. (and 2 more errors)',
        ],
        'when respondent_name is missing' => [
            'data' => [...$data, 'respondent_name' => null],
            'exception' => ValidationException::class,
            'message' => 'The respondent name field is required.',
        ],
        'when respondent_email is missing' => [
            'data' => [...$data, 'respondent_email' => null],
            'exception' => ValidationException::class,
            'message' => 'The respondent email field is required.',
        ],
        'when respondent_email is invalid' => [
            'data' => [...$data, 'respondent_email' => 'not-an-email'],
            'exception' => ValidationException::class,
            'message' => 'The respondent email field must be a valid email address.',
        ],
        'when responses is missing' => [
            'data' => [...$data, 'responses' => null],
            'exception' => ValidationException::class,
            'message' => 'The responses field is required.',
        ],
        'when responses is not an array' => [
            'data' => [...$data, 'responses' => 'not-an-array'],
            'exception' => ValidationException::class,
            'message' => 'The responses field must be an array.',
        ],
        'when survey_field_id is missing' => [
            'data' => [...$data, 'responses' => [['response' => 'Red']]],
            'exception' => ValidationException::class,
            'message' => 'The responses.0.survey_field_id field is required.',
        ],
        'when response is missing' => [
            'data' => [...$data, 'responses' => [['survey_field_id' => 1]]],
            'exception' => ValidationException::class,
            'message' => 'The responses.0.response field is required.',
        ],
    ];
});
