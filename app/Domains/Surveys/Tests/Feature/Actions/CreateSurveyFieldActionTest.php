<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Actions;

use App\Domains\Surveys\Actions\CreateSurveyFieldAction;
use App\Domains\Surveys\Data\SurveyFieldData;
use App\Domains\Surveys\Data\SurveyFieldRequestData;
use App\Domains\Surveys\Enums\SurveyFieldTypeEnum;
use App\Domains\Surveys\Models\Survey;
use App\Models\User;


describe('CreateSurveyFieldAction', function () {
    it('creates a survey field with the given data', function () {
        $survey = Survey::factory()->create();
        $user = User::factory()->create();

        $data = SurveyFieldRequestData::from([
            'survey_id' => $survey->id,
            'name' => 'Favorite Color',
            'type' => SurveyFieldTypeEnum::Text,
            'options' => [],
        ]);

        $action = new CreateSurveyFieldAction();
        $fieldData = $action->execute($user, $data);

        expect($fieldData)->toBeInstanceOf(SurveyFieldData::class);
        expect($fieldData->name)->toBe('Favorite Color');
        expect($fieldData->type)->toBe(SurveyFieldTypeEnum::Text);
        expect($fieldData->survey->id)->toBe($survey->id);
        expect($fieldData->createdBy->id)->toBe($user->id);
    });
});
