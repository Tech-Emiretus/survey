<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class SurveyResponseRequestData extends Data
{
    public function __construct(
        public string $respondentName,
        public string $respondentEmail,
        public array $responses,
    ) {
    }

    public static function rules(): array
    {
        return [
            'respondent_name' => ['required', 'string', 'max:255'],
            'respondent_email' => ['required', 'email', 'max:255'],
            'responses' => ['required', 'array'],
            'responses.*.survey_field_id' => ['required', 'integer', 'exists:survey_fields,id'],
            'responses.*.response' => ['required', 'string', 'max:65535'],
        ];
    }
}
