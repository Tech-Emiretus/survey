<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Actions;

use App\Domains\Surveys\Data\SurveyResponseRequestData;
use App\Domains\Surveys\Models\Survey;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateSurveyResponseAction
{
    public function execute(Survey $survey, SurveyResponseRequestData $data): void
    {
        if (!$survey->isActive()) {
            throw new BadRequestException('This survey is not active.', 400);
        }

        if ($this->alreadyResponded($survey, $data->respondentEmail)) {
            throw new BadRequestException('You have already responded to this survey.', 400);
        }

        // @todo: validate that all fields of the survey  have answers. I'll skip this for now.

        DB::transaction(function () use ($survey, $data) {
            $response = $survey->responses()->create([
                'respondent_name' => $data->respondentName,
                'respondent_email' => $data->respondentEmail,
            ]);

            $response->fieldResponses()->createMany($data->responses);
        });
    }

    private function alreadyResponded(Survey $survey, string $email): bool
    {
        return $survey->responses()
            ->where('respondent_email', $email)
            ->exists();
    }
}
