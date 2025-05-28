<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Actions;

use App\Domains\Surveys\Actions\CreateSurveyAction;
use App\Domains\Surveys\Data\SurveyData;
use App\Domains\Surveys\Data\SurveyRequestData;
use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;

describe('CreateSurveyAction', function () {
    it('throws an exception if the user does not belong to the company', function () {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $data = SurveyRequestData::from([
            'company_id' => $company->id,
            'title' => 'Test Survey',
            'description' => 'This is a test survey description.',
            'start_at' => now(),
            'end_at' => now()->addDays(7),
        ]);

        $action = new CreateSurveyAction();

        expect(fn () => $action->execute($user, $data))
            ->toThrow(UnauthorizedException::class, 'User does not belong to the specified company.');
    });

    it('creates a survey with the given data', function () {
        $company = Company::factory()->create();
        $user = User::factory()->hasAttached($company)->create();

        $data = SurveyRequestData::from([
            'company_id' => $company->id,
            'title' => 'Test Survey',
            'description' => 'This is a test survey description.',
            'start_at' => now(),
            'end_at' => now()->addDays(7),
        ]);

        $action = new CreateSurveyAction();
        $surveyData = $action->execute($user, $data);

        expect($surveyData)->toBeInstanceOf(SurveyData::class);
        expect($surveyData->title)->toBe('Test Survey');
        expect($surveyData->description)->toBe('This is a test survey description.');
        expect($surveyData->companyId)->toBe($company->id);
        expect($surveyData->status)->toBe(SurveyStatusEnum::Draft);
        expect($surveyData->createdBy)->toBe($user->id);
    });
});
