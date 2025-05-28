<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Actions;

use App\Domains\Surveys\Actions\CreateSurveyResponseAction;
use App\Domains\Surveys\Data\SurveyResponseRequestData;
use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyField;
use App\Domains\Surveys\Models\SurveyResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

describe('CreateSurveyResponseAction', function () {
    it('throws an exception if the survey is not active', function () {
        $survey = Survey::factory()->create(['status' => SurveyStatusEnum::Draft]);
        $field = SurveyField::factory()->create(['survey_id' => $survey->id]);

        $data = SurveyResponseRequestData::from([
            'respondent_name' => 'John Doe',
            'respondent_email' => 'john@example.com',
            'responses' => [
                [
                    'survey_field_id' => $field->id,
                    'response' => 'Blue',
                ],
            ],
        ]);

        $action = new CreateSurveyResponseAction();
        expect(fn () => $action->execute($survey, $data))
            ->toThrow(BadRequestException::class, 'This survey is not active.');
    });

    it('throws an exception if the respondent has already responded', function () {
        $survey = Survey::factory()->create([
            'status' => SurveyStatusEnum::Published,
            'start_at' => now()->subDays(1),
            'end_at' => now()->addDays(1),
        ]);

        $field = SurveyField::factory()->create(['survey_id' => $survey->id]);

        // Existing response for the same email
        SurveyResponse::factory()->create([
            'survey_id' => $survey->id,
            'respondent_email' => 'john@example.com',
        ]);

        $data = SurveyResponseRequestData::from([
            'respondent_name' => 'John Doe',
            'respondent_email' => 'john@example.com',
            'responses' => [
                [
                    'survey_field_id' => $field->id,
                    'response' => 'Blue',
                ],
            ],
        ]);

        $action = new CreateSurveyResponseAction();
        expect(fn () => $action->execute($survey, $data))
            ->toThrow(BadRequestException::class, 'You have already responded to this survey.');
    });

    it('creates a survey response with the given data', function () {
        $survey = Survey::factory()->create([
            'status' => SurveyStatusEnum::Published,
            'start_at' => now()->subDays(1),
            'end_at' => now()->addDays(1),
        ]);

        $field = SurveyField::factory()->create(['survey_id' => $survey->id]);

        $data = SurveyResponseRequestData::from([
            'respondent_name' => 'Jane Doe',
            'respondent_email' => 'jane@example.com',
            'responses' => [
                [
                    'survey_field_id' => $field->id,
                    'response' => 'Red',
                ],
            ],
        ]);

        $action = new CreateSurveyResponseAction();
        $action->execute($survey, $data);

        $response = SurveyResponse::where('survey_id', $survey->id)
            ->where('respondent_email', 'jane@example.com')
            ->first();

        expect($response)->not()->toBeNull();
        expect($response->respondent_name)->toBe('Jane Doe');
        expect($response->fieldResponses)->toHaveCount(1);
        expect($response->fieldResponses->first()->survey_field_id)->toBe($field->id);
        expect($response->fieldResponses->first()->response)->toBe('Red');
    });
});
