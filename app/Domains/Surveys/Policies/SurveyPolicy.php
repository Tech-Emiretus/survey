<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Policies;

use App\Domains\Surveys\Models\Survey;
use App\Models\User;

class SurveyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }

    public function delete(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }

    public function restore(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }

    public function forceDelete(User $user, Survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }
}
