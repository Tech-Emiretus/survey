<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Actions;

use App\Domains\Surveys\Data\SurveyFieldData;
use App\Domains\Surveys\Data\SurveyFieldRequestData;
use App\Domains\Surveys\Models\SurveyField;
use App\Models\User;

class CreateSurveyFieldAction
{
    public function execute(User $user, SurveyFieldRequestData $data): SurveyFieldData
    {
        $surveyField = SurveyField::create([
            ...$data->toArray(),
            'created_by' => $user->id,
        ]);

        return SurveyFieldData::from($surveyField->load('survey', 'createdBy', 'deletedBy'));
    }
}
