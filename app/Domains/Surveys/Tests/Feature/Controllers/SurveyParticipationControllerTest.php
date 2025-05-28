<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Controllers;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyResponse;
use App\Models\Company;
use App\Models\User;
use Tests\TestCase;

describe('SurveyParticipationController', function (): void {
    describe('show', function (): void {
        it('will return not found when survey does not exist', function (): void {
            /** @var TestCase $this */

            $response = $this->getJson(route('domains.participate.show', ['survey' => 'invalid-id']));

            $response->assertNotFound();
        });

        it('will return bad request when survey is not published', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create(['status' => SurveyStatusEnum::Draft->value]);
            $response = $this->getJson(route('domains.participate.show', ['survey' => $survey->public_id]));

            $response->assertBadRequest()
                ->assertJson([
                    'success' => false,
                    'message' => 'This survey is not active.',
                ]);
        });

        it('will return bad request when survey has not started yet', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create([
                'status' => SurveyStatusEnum::Published->value,
                'start_at' => now()->addDays(1),
            ]);
            $response = $this->getJson(route('domains.participate.show', ['survey' => $survey->public_id]));

            $response->assertBadRequest()
                ->assertJson([
                    'success' => false,
                    'message' => 'This survey is not active.',
                ]);
        });

        it('will return bad request when survey has ended', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create([
                'status' => SurveyStatusEnum::Published->value,
                'start_at' => now()->subDays(7),
                'end_at' => now()->subDays(1),
            ]);
            $response = $this->getJson(route('domains.participate.show', ['survey' => $survey->public_id]));

            $response->assertBadRequest()
                ->assertJson([
                    'success' => false,
                    'message' => 'This survey is not active.',
                ]);
        });

        it('will return a survey with its fields and relationships when survey is active', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();

            $survey = Survey::factory()->create([
                'company_id' => $company->id,
                'created_by' => $user->id,
                'status' => SurveyStatusEnum::Published->value,
                'start_at' => now()->subDays(1),
                'end_at' => now()->addDays(7),
            ]);

            $surveyFields = $survey->fields()->createMany([
                ['name' => 'Favorite Color', 'type' => 'text', 'options' => [], 'created_by' => $user->id],
                ['name' => 'Age', 'type' => 'text', 'options' => [], 'created_by' => $user->id],
            ]);

            $response = $this->getJson(route('domains.participate.show', ['survey' => $survey->public_id]));

            $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'message' => '',
                    'data' => [
                        'id' => $survey->id,
                        'title' => $survey->title,
                        'description' => $survey->description,
                        'company' => [
                            'id' => $company->id,
                            'name' => $company->name,
                        ],
                        'created_by' => [
                            'id' => $user->id,
                            'name' => $user->name,
                        ],
                        'fields' => [
                            ['id' => $surveyFields[0]->id, 'name' => 'Favorite Color', 'type' => 'text', 'options' => []],
                            ['id' => $surveyFields[1]->id, 'name' => 'Age', 'type' => 'text', 'options' => []],
                        ],
                    ],
                ]);
        });
    });

    describe('store', function (): void {
        it('will return bad request when survey is not active', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create(['status' => SurveyStatusEnum::Draft->value]);
            $surveyField = $survey->fields()->create([
                'name' => 'Favorite Color',
                'type' => 'text',
                'options' => [],
                'created_by' => $survey->created_by,
            ]);

            $data = [
                'respondent_name' => 'John Doe',
                'respondent_email' => 'john@example.com',
                'responses' => [
                    [
                        'survey_field_id' => $surveyField->id,
                        'response' => 'Blue',
                    ],
                ],
            ];

            $this->postJson(route('domains.participate.store', ['survey' => $survey->public_id]), $data)
                ->assertBadRequest()
                ->assertJson([
                    'success' => false,
                    'message' => 'This survey is not active.',
                ]);
        });

        it('will return validation errors when data is invalid', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->active()->create();
            $data = [
                'respondent_name' => '',
                'respondent_email' => 'not-an-email',
                'responses' => 'not-an-array',
            ];

            $this->postJson(route('domains.participate.store', ['survey' => $survey->public_id]), $data)
                ->assertJsonValidationErrors([
                    'respondent_name',
                    'respondent_email',
                    'responses',
                ])
                ->assertUnprocessable();
        });

        it('will return bad request if respondent has already responded', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->active()->create();
            $field = $survey->fields()->create([
                'name' => 'Favorite Color',
                'type' => 'text',
                'options' => [],
                'created_by' => $survey->created_by,
            ]);

            $survey->responses()->create([
                'respondent_name' => 'Jane Doe',
                'respondent_email' => 'jane@example.com',
            ]);

            $data = [
                'respondent_name' => 'Jane Doe',
                'respondent_email' => 'jane@example.com',
                'responses' => [
                    [
                        'survey_field_id' => $field->id,
                        'response' => 'Blue',
                    ],
                ],
            ];
            $response = $this->postJson(route('domains.participate.store', ['survey' => $survey->public_id]), $data);
            $response->assertBadRequest()
                ->assertJson([
                    'success' => false,
                    'message' => 'You have already responded to this survey.',
                ]);
        });

        it('will create a survey response with valid data', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->active()->create();
            $field = $survey->fields()->create([
                'name' => 'Favorite Color',
                'type' => 'text',
                'options' => [],
                'created_by' => $survey->created_by,
            ]);

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

            $this->postJson(route('domains.participate.store', ['survey' => $survey->public_id]), $data)
                ->assertCreated()
                ->assertJson([
                    'success' => true,
                    'message' => 'Survey response created successfully.',
                ]);

            expect(SurveyResponse::all())->toHaveCount(1)
                ->and(SurveyResponse::first()->respondent_name)->toBe('John Doe')
                ->and(SurveyResponse::first()->respondent_email)->toBe('john@example.com');
        });
    });
});
