<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Factories;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'status' => SurveyStatusEnum::Draft,
            'start_at' => now(),
            'end_at' => now()->addDays(7),
            'created_by' => User::factory(),
            'deleted_by' => null,
        ];
    }

    public function active(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => SurveyStatusEnum::Published,
                'start_at' => now()->subDays(1),
                'end_at' => now()->addDays(7),
            ];
        });
    }
}
