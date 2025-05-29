<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Controllers;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveySummary;
use App\Models\Company;
use App\Models\User;
use Tests\TestCase;

describe('SurveySummaryController', function () {
    describe('index', function (): void {
        it('returns unauthenticated when not logged in', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id]);

            $this->getJson(route('domains.surveys.summaries.index', ['survey' => $survey->id]))
                ->assertUnauthorized();
        });

        it('returns forbidden when user does not belong to the company', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);

            $this->actingAs($user)
                ->getJson(route('domains.surveys.summaries.index', ['survey' => $survey->id]))
                ->assertForbidden();
        });

        it('returns survey summaries for the authenticated user', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $summary = SurveySummary::factory()->create(['survey_id' => $survey->id, 'created_by' => $user->id]);
            $this->actingAs($user);

            $this->getJson(route('domains.surveys.summaries.index', ['survey' => $survey->id]))
                ->assertOk()
                ->assertJson([
                    'success' => true,
                    'message' => null,
                    'data' => [
                        [
                            'id' => $summary->id,
                            'survey_id' => $survey->id,
                            'created_by' => [
                                'id' => $user->id,
                            ],
                            'status' => SurveySummaryStatusEnum::Pending->value,
                        ],
                    ],
                ]);
        });
    });

    describe('store', function (): void {
        it('returns unauthenticated when not logged in', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id]);

            $this->postJson(route('domains.surveys.summaries.store', ['survey' => $survey->id]))
                ->assertUnauthorized();
        });

        it('returns forbidden when user does not belong to the company', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $this->actingAs($user)
                ->postJson(route('domains.surveys.summaries.store', ['survey' => $survey->id]))
                ->assertForbidden();
        });

        it('creates a new survey summary for the authenticated user', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $this->actingAs($user);

            $this->postJson(route('domains.surveys.summaries.store', ['survey' => $survey->id]))
                ->assertCreated()
                ->assertJson([
                    'success' => true,
                    'message' => 'Survey summary created.',
                    'data' => [
                        'survey_id' => $survey->id,
                        'status' => SurveySummaryStatusEnum::Pending->value,
                        'created_by' => $user->id,
                    ],
                ]);
        });
    });

    describe('show', function (): void {
        it('returns unauthenticated when not logged in', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id]);
            $summary = SurveySummary::factory()->create(['survey_id' => $survey->id]);

            $this->getJson(route('domains.surveys.summaries.show', ['survey' => $survey->id, 'summary' => $summary->id]))
                ->assertUnauthorized();
        });

        it('returns forbidden when user does not belong to the company', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $summary = SurveySummary::factory()->create(['survey_id' => $survey->id]);
            $this->actingAs($user)
                ->getJson(route('domains.surveys.summaries.show', ['survey' => $survey->id, 'summary' => $summary->id]))
                ->assertForbidden();
        });

        it('returns the survey summary for the authenticated user', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $summary = SurveySummary::factory()->create(['survey_id' => $survey->id, 'created_by' => $user->id]);
            $this->actingAs($user);

            $this->getJson(route('domains.surveys.summaries.show', ['survey' => $survey->id, 'summary' => $summary->id]))
                ->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $summary->id,
                        'survey_id' => $survey->id,
                        'status' => SurveySummaryStatusEnum::Pending->value,
                        'created_by' => [
                            'id' => $user->id,
                        ],
                    ],
                ]);
        });
    });
});
