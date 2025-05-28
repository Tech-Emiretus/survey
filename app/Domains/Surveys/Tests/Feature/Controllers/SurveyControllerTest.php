<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Controllers;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

describe('SurveyController', function (): void {
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
                'company_id'
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
                    ]
                ]);
        });
    });
});
