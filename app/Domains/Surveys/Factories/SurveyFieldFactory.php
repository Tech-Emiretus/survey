<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Factories;

use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyField;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyField>
 */
class SurveyFieldFactory extends Factory
{
    protected $model = SurveyField::class;

    public function definition(): array
    {
        $types = ['text', 'textarea', 'select', 'checkbox', 'radio'];
        $type = $this->faker->randomElement($types);
        $options = in_array($type, ['select', 'checkbox', 'radio']) ? $this->faker->words(3) : null;
        return [
            'survey_id' => Survey::factory(),
            'name' => $this->faker->word(),
            'type' => $type,
            'options' => $options,
            'created_by' => User::factory(),
            'deleted_by' => null,
        ];
    }
}
