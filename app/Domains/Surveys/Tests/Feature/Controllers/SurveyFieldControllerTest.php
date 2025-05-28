<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Controllers;

use App\Domains\Surveys\Enums\SurveyFieldTypeEnum;
use App\Domains\Surveys\Models\Survey;
use App\Models\Company;
use App\Models\User;
use Tests\TestCase;

describe('SurveyFieldController', function (): void {
    describe('store', function (): void {
        it('will return unauthenticated when user is not logged in', function (): void {
            /** @var TestCase $this */

            $response = $this->postJson(route('domains.surveys.fields.store', ['survey' => 1]));
            $response->assertUnauthorized();
        });

        it('will return forbidden when user does not have access to the survey', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $this->actingAs($user);
            $data = [
                'name' => 'Favorite Color',
                'type' => SurveyFieldTypeEnum::Text->value,
                'options' => [],
            ];
            $response = $this->postJson(route('domains.surveys.fields.store', ['survey' => $survey->id]), $data);
            $response->assertForbidden();
        });

        it('will return validation errors when data is invalid', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);

            $this->actingAs($user);

            $data = [
                'name' => 'Favorite Color',
                'type' => 'select',
            ];

            $response = $this->postJson(route('domains.surveys.fields.store', ['survey' => $survey->id]), $data);
            $response->assertJsonValidationErrors(['type'])->assertUnprocessable();
        });

        it('will create a survey field with valid data', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);

            $this->actingAs($user);

            $data = [
                'name' => 'Favorite Color',
                'type' => SurveyFieldTypeEnum::Text->value,
                'options' => [],
            ];

            $this->postJson(route('domains.surveys.fields.store', ['survey' => $survey->id]), $data)
                ->assertCreated()
                ->assertJson([
                    'success' => true,
                    'message' => 'Survey field created successfully.',
                    'data' => [
                        'survey' => [
                            'id' => $survey->id,
                        ],
                        'name' => 'Favorite Color',
                        'type' => SurveyFieldTypeEnum::Text->value,
                        'options' => [],
                    ],
                ]);
        });
    });
});
