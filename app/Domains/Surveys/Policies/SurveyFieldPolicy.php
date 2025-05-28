<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Policies;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyField;
use App\Models\User;

class SurveyFieldPolicy
{
    public function viewAny(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id);
    }

    public function view(User $user, SurveyField $field, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $field->survey_id === $survey->id;
    }

    public function create(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $survey->status === SurveyStatusEnum::Draft;
    }

    public function update(User $user, SurveyField $field): bool
    {
        return false;
    }

    public function delete(User $user, SurveyField $field, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id)
            && $survey->status === SurveyStatusEnum::Draft
            && $field->survey_id === $survey->id;
    }

    public function restore(User $user, SurveyField $field): bool
    {
        return false;
    }

    public function forceDelete(User $user, SurveyField $field): bool
    {
        return false;
    }
}
