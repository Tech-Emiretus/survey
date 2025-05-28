<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Factories;

use App\Domains\Surveys\Models\SurveyField;
use App\Domains\Surveys\Models\SurveyFieldResponse;
use App\Domains\Surveys\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyFieldResponse>
 */
class SurveyFieldResponseFactory extends Factory
{
    protected $model = SurveyFieldResponse::class;

    public function definition(): array
    {
        return [
            'survey_response_id' => SurveyResponse::factory(),
            'survey_field_id' => SurveyField::factory(),
            'response' => $this->faker->optional()->sentence(),
        ];
    }
}
