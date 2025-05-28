<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Actions;

use App\Domains\Surveys\Data\SurveyData;
use App\Domains\Surveys\Data\SurveyRequestData;
use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;

class CreateSurveyAction
{
    public function execute(User $user, SurveyRequestData $data): SurveyData
    {
        if (!$user->hasCompany($data->companyId)) {
            throw new UnauthorizedException('User does not belong to the specified company.');
        }

        $survey = Survey::create([
            ...$data->toArray(),
            'status' => SurveyStatusEnum::Draft,
            'created_by' => $user->id,
        ]);

        return SurveyData::from($survey);
    }
}
