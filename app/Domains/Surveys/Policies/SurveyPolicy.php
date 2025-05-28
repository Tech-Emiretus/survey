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

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, survey $survey): bool
    {
        return $user->hasCompany($survey->company_id) && $user->id === $survey->created_by;
    }
}
