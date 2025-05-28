<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Factories;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveySummary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveySummary>
 */
class SurveySummaryFactory extends Factory
{
    protected $model = SurveySummary::class;

    public function definition(): array
    {
        return [
            'survey_id' => Survey::factory(),
            'total_responses' => $this->faker->numberBetween(0, 1000),
            'sentiment' => $this->faker->optional()->randomElement(['positive', 'neutral', 'negative']),
            'summary' => $this->faker->optional()->paragraph(),
            'status' => SurveySummaryStatusEnum::Pending->value,
            'created_by' => User::factory(),
        ];
    }
}
