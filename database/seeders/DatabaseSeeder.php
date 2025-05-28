<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Surveys\Enums\SurveyFieldTypeEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveyResponse;
use App\Models\Company;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->truncateTables();

        $company = Company::factory()->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $survey = Survey::factory()->create([
            'title' => 'Test - Leadership Survey',
            'description' => 'This is a test survey for leadership feedback.',
            'company_id' => $company->id,
            'created_by' => $user->id,
        ]);

        $radioOptions = [
            'Excellent',
            'Good',
            'Average',
            'Poor',
            'Very Poor',
        ];

        $survey->fields()->createMany([
            ['name' => 'Leadership Skills', 'type' => SurveyFieldTypeEnum::Radio, 'options' => $radioOptions, 'created_by' => $user->id],
            ['name' => 'Communication', 'type' => SurveyFieldTypeEnum::Radio, 'options' => $radioOptions, 'created_by' => $user->id],
            ['name' => 'Team Collaboration', 'type' => SurveyFieldTypeEnum::Radio, 'options' => $radioOptions, 'created_by' => $user->id],
            ['name' => 'Problem Solving', 'type' => SurveyFieldTypeEnum::Radio, 'options' => $radioOptions, 'created_by' => $user->id],
            ['name' => 'Innovation', 'type' => SurveyFieldTypeEnum::Radio, 'options' => $radioOptions, 'created_by' => $user->id],
            ['name' => 'Overall Satisfaction', 'type' => SurveyFieldTypeEnum::Radio, 'options' => $radioOptions, 'created_by' => $user->id],
        ]);

        $responses = SurveyResponse::factory()->count(10)->create(['survey_id' => $survey->id]);
        foreach ($responses as $response) {
            $response->fieldResponses()->createMany($survey->fields->map(fn ($field) => [
                'survey_field_id' => $field->id,
                'response' => fake()->randomElement($radioOptions),
            ])->toArray());
        }
    }

    private function truncateTables(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('survey_field_responses')->truncate();
        DB::table('survey_fields')->truncate();
        DB::table('survey_responses')->truncate();
        DB::table('surveys')->truncate();
        DB::table('companies')->truncate();
        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();
    }
}
