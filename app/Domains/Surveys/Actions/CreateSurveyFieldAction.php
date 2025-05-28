<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Actions;

use App\Domains\Surveys\Data\SurveyFieldData;
use App\Domains\Surveys\Data\SurveyFieldRequestData;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyField;
use App\Models\User;

class CreateSurveyFieldAction
{
    public function execute(User $user, Survey $survey, SurveyFieldRequestData $data): SurveyFieldData
    {
        $surveyField = SurveyField::create([
            ...$data->toArray(),
            'survey_id' => $survey->id,
            'created_by' => $user->id,
        ]);

        return SurveyFieldData::from($surveyField->load('survey', 'createdBy', 'deletedBy'));
    }
}
