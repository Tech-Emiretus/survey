<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Controllers;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

describe('SurveyController', function (): void {
    describe('index', function (): void {
        it('will return unauthenticated when user is not logged in', function (): void {
            /** @var TestCase $this */

            $response = $this->getJson(route('domains.surveys.index'));
            $response->assertUnauthorized();
        });

        it('will return an empty list of surveys when user`s company has no surveys', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $this->actingAs($user);

            Survey::factory()->count(3)->create();

            $response = $this->getJson(route('domains.surveys.index'));

            $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'message' => '',
                    'data' => [],
                ]);

            expect($response->json('data'))->toBeEmpty();
            expect(Survey::count())->toBe(3);
        });

        it('will return a list of surveys for the user`s company', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $this->actingAs($user);

            $surveys = Survey::factory()->count(3)->create(['company_id' => $company->id]);

            $response = $this->getJson(route('domains.surveys.index'));

            $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'message' => '',
                    'data' => [
                        ['id' => $surveys[0]->id, 'title' => $surveys[0]->title],
                        ['id' => $surveys[1]->id, 'title' => $surveys[1]->title],
                        ['id' => $surveys[2]->id, 'title' => $surveys[2]->title],
                    ],
                ]);
        });
    });

    describe('store', function (): void {
        it('will return unauthenticated when user is not logged in', function (): void {
            /** @var TestCase $this */

            $response = $this->postJson(route('domains.surveys.store'));

            $response->assertUnauthorized();
        });

        it('will return validation errors when data is invalid', function (): void {
            /** @var TestCase $this */

            $user = User::factory()->create();
            $this->actingAs($user);

            $data = [
                'title' => 'Test Survey',
                'company_id' => 999,
                'description' => 'This is a test survey.',
            ];

            $response = $this->postJson(route('domains.surveys.store'), $data);

            $response->assertJsonValidationErrors([
                'start_at',
                'end_at',
                'company_id',
            ])->assertUnprocessable();
        });

        it('will create a survey with valid data', function (): void {
            Carbon::setTestNow('2025-05-27 00:00:00');

            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $this->actingAs($user);

            $data = [
                'title' => 'Test Survey',
                'company_id' => $company->id,
                'description' => 'This is a test survey.',
                'start_at' => now()->toISOString(),
                'end_at' => now()->addDays(7)->toISOString(),
            ];

            $this->postJson(route('domains.surveys.store'), $data)
                ->assertCreated()
                ->assertJson([
                    'success' => true,
                    'message' => 'Survey created successfully.',
                    'data' => [
                        'company' => [
                            'id' => $company->id,
                            'name' => $company->name,
                        ],
                        'title' => 'Test Survey',
                        'description' => 'This is a test survey.',
                        'created_by' => [
                            'id' => $user->id,
                            'name' => $user->name,
                        ],
                        'status' => SurveyStatusEnum::Draft->value,
                        'start_at' => '2025-05-27T00:00:00+00:00',
                        'end_at' => '2025-06-03T00:00:00+00:00',
                    ],
                ]);
        });
    });

    describe('show', function (): void {
        it('will return unauthenticated when user is not logged in', function (): void {
            /** @var TestCase $this */

            $survey = Survey::factory()->create();
            $response = $this->getJson(route('domains.surveys.show', ['survey' => $survey->id]));

            $response->assertUnauthorized();
        });

        it('will return unauthorized when user does not have permission to view the survey', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $this->actingAs($user);

            $survey = Survey::factory()->create();
            $response = $this->getJson(route('domains.surveys.show', ['survey' => $survey->id]));
            $response->assertForbidden();
        });

        it('will return a survey with its fields and relationships', function (): void {
            /** @var TestCase $this */

            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $this->actingAs($user);

            $survey = Survey::factory()->create(['company_id' => $company->id, 'created_by' => $user->id]);
            $surveyFields = $survey->fields()->createMany([
                ['name' => 'Favorite Color', 'type' => 'text', 'options' => [], 'created_by' => $user->id],
                ['name' => 'Age', 'type' => 'text', 'options' => [], 'created_by' => $user->id],
            ]);

            $response = $this->getJson(route('domains.surveys.show', ['survey' => $survey->id]));

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

    describe('update', function (): void {
        it('returns unauthenticated when user is not logged in', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();

            $this->putJson(route('domains.surveys.update', ['survey' => $survey->id]))
                ->assertUnauthorized();
        });

        it('returns forbidden when user does not have permission to update the survey', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id]);

            $this->actingAs($user);

            $data = [
                'title' => 'Updated Survey',
                'description' => 'Updated description.',
            ];

            $this->putJson(route('domains.surveys.update', ['survey' => $survey->id]), $data)
                ->assertForbidden();
        });

        it('updates a survey with valid data', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();

            $survey = Survey::factory()->create([
                'company_id' => $company->id,
                'created_by' => $user->id,
                'status' => SurveyStatusEnum::Draft->value,
            ]);

            $this->actingAs($user);

            $data = [
                'company_id' => $company->id,
                'title' => 'Updated Survey',
                'description' => 'Updated description.',
                'start_at' => now()->toISOString(),
                'end_at' => now()->addDays(7)->toISOString(),
            ];

            $this->putJson(route('domains.surveys.update', ['survey' => $survey->id]), $data)
                ->assertOk()
                ->assertJson([
                    'success' => true,
                    'message' => 'Survey updated successfully.',
                    'data' => [
                        'id' => $survey->id,
                        'title' => 'Updated Survey',
                        'description' => 'Updated description.',
                        'company' => [
                            'id' => $company->id,
                            'name' => $company->name,
                        ],
                        'created_by' => [
                            'id' => $user->id,
                            'name' => $user->name,
                        ],
                    ],
                ]);
        });
    });

    describe('destroy', function (): void {
        it('returns unauthenticated when user is not logged in', function (): void {
            /** @var TestCase $this */
            $survey = Survey::factory()->create();

            $this->deleteJson(route('domains.surveys.destroy', ['survey' => $survey->id]))
                ->assertUnauthorized();
        });

        it('returns forbidden when user does not have permission to delete the survey', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create(['company_id' => $company->id]);

            $this->actingAs($user);

            $this->deleteJson(route('domains.surveys.destroy', ['survey' => $survey->id]))
                ->assertForbidden();
        });

        it('deletes a survey', function (): void {
            /** @var TestCase $this */
            $company = Company::factory()->create();
            $user = User::factory()->hasAttached($company)->create();
            $survey = Survey::factory()->create([
                'company_id' => $company->id,
                'created_by' => $user->id,
                'status' => SurveyStatusEnum::Draft->value,
            ]);
            $this->actingAs($user);

            $this->deleteJson(route('domains.surveys.destroy', ['survey' => $survey->id]))
                ->assertNoContent();

            $survey->refresh();

            $this->assertSoftDeleted($survey);
            $this->assertEquals($user->id, $survey->deleted_by);
        });
    });
});
