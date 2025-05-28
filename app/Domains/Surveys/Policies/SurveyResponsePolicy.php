<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Policies;

use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyResponse;
use App\Models\User;

class SurveyResponsePolicy
{
    public function viewAny(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id);
    }

    public function view(User $user, SurveyResponse $response, $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $response->survey_id === $survey->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Survey $survey): bool
    {
        return false;
    }

    public function delete(User $user, Survey $survey): bool
    {
        return false;
    }

    public function restore(User $user, Survey $survey): bool
    {
        return false;
    }

    public function forceDelete(User $user, Survey $survey): bool
    {
        return false;
    }
}
