<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Controllers;

use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyFieldResponse;
use App\Domains\Surveys\Models\SurveyResponse;
use App\Models\User;
use Tests\TestCase;

describe('SurveyResponseController', function (): void {
    describe('index', function (): void {
        it('returns unauthenticated when user is not authenticated', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();
            $response = $this->getJson(route('domains.surveys.responses.index', ['survey' => $survey->id]));
            $response->assertUnauthorized();
        });

        it('returns forbidden when user does not have access to the survey', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create();
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->getJson(route('domains.surveys.responses.index', ['survey' => $survey->id]));

            $response->assertForbidden();
        });

        it('returns a list of survey responses for a survey', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create();
            $user = User::factory()->hasAttached($survey->company)->create();
            $this->actingAs($user);

            $responses = SurveyResponse::factory()->count(2)->create(['survey_id' => $survey->id]);

            $this->getJson(route('domains.surveys.responses.index', ['survey' => $survey->id]))
                ->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => $responses->toArray(),
                ]);
        });
    });

    describe('show', function (): void {
        it('returns unauthenticated when user is not authenticated', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();
            $response = SurveyResponse::factory()->create(['survey_id' => $survey->id]);

            $this->getJson(route('domains.surveys.responses.show', ['survey' => $survey->id, 'response' => $response->id]))
                ->assertUnauthorized();
        });

        it('returns forbidden when user does not have access to the survey', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = SurveyResponse::factory()->create(['survey_id' => $survey->id]);
            $this->getJson(route('domains.surveys.responses.show', ['survey' => $survey->id, 'response' => $response->id]))
                ->assertForbidden();
        });

        it('returns forbidden when response does not belong to the survey', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();
            $user = User::factory()->hasAttached($survey->company)->create();
            $this->actingAs($user);

            $response = SurveyResponse::factory()->create(['survey_id' => Survey::factory()->create()->id]);

            $this->getJson(route('domains.surveys.responses.show', ['survey' => $survey->id, 'response' => $response->id]))
                ->assertForbidden();
        });

        it('returns a single survey response with its field responses', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();
            $user = User::factory()->hasAttached($survey->company)->create();
            $this->actingAs($user);

            $response = SurveyResponse::factory()->create(['survey_id' => $survey->id]);
            $fieldResponse = SurveyFieldResponse::factory()->create(['survey_response_id' => $response->id]);

            $this->getJson(route('domains.surveys.responses.show', ['survey' => $survey->id, 'response' => $response->id]))
                ->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => array_merge($response->toArray(), [
                        'field_responses' => [$fieldResponse->toArray()],
                    ]),
                ]);
        });
    });
});
