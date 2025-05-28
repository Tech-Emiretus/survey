<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Actions;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveySummary;
use App\Domains\Surveys\Notifications\SurveySummaryCompleted;
use App\Domains\Surveys\Notifications\SurveySummaryFailed;
use Exception;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

// phpcs:disable Generic.Files.LineLength
class GenerateSurveySummaryAction
{
    public function __construct(
        private ?string $openAiModel = null,
    ) {
        $this->openAiModel = $openAiModel ?: config('openai.model');
    }

    public function execute(SurveySummary $surveySummary): void
    {
        try {
            $surveySummary->update(['status' => SurveySummaryStatusEnum::Generating]);
            $surveySummary->load([
                'survey' => fn ($query) => $query->with([
                    'fields' => fn ($query) => $query->orderBy('id'),
                    'responses.fieldResponses' => fn ($query) => $query->with('surveyField')->orderBy('survey_field_id'),
                ]),
            ]);

            $survey = $surveySummary->survey;

            if (!$survey) {
                throw new Exception('Survey not found for the provided survey summary.');
            }

            $openAiResponse = $this->getSummaryFromOpenAi($survey);

            if (empty($openAiResponse)) {
                throw new Exception('Failed to generate summary from OpenAI.');
            }

            ['sentiment' => $sentiment, 'summary' => $summary] = json_decode($openAiResponse, true);

            if (empty($sentiment) || empty($summary)) {
                throw new Exception('Invalid response format from OpenAI. Expected sentiment and summary.');
            }

            $surveySummary->update([
                'sentiment' => $sentiment,
                'summary' => $summary,
                'status' => SurveySummaryStatusEnum::Completed,
                'completed_at' => now(),
                'error_message' => null,
            ]);

            $surveySummary->createdBy->notify(new SurveySummaryCompleted($surveySummary));
        } catch (Throwable $th) {
            $surveySummary->update([
                'status' => SurveySummaryStatusEnum::Failed,
                'error_message' => $th->getMessage(),
                'completed_at' => now(),
            ]);

            $surveySummary->createdBy->notify(new SurveySummaryFailed($surveySummary));

            throw $th;
        }
    }

    private function getSummaryFromOpenAi(Survey $survey)
    {
        $response = OpenAI::chat()->create([
            'model' => $this->openAiModel,
            'messages' => [
                ['role' => 'system', 'content' => $this->getSystemPrompt()],
                ['role' => 'user', 'content' => $this->getPrompt($survey)],
            ],
        ]);

        return $response->choices[0]->message->content ?? '';
    }


    private function formatResponsesForPrompt(Survey $survey): array
    {
        if ($survey->responses->isEmpty()) {
            return [];
        }

        $headers = $survey->fields->pluck('name')->toArray();
        $data = [$headers];

        foreach ($survey->responses as $response) {
            $formattedResponses = $response->fieldResponses
                ->map(fn ($response) => $response->response)
                ->values()
                ->toArray();

            $data[] = $formattedResponses;
        }

        return $data;
    }

    private function getPrompt(Survey $survey): string
    {
        $responses = $this->formatResponsesForPrompt($survey);
        $responseCsv = array_map(
            fn ($row) => implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', $row)),
            $responses
        );
        $csvContent = implode("\n", $responseCsv);

        return <<<PROMPT
        Survey title: {$survey->title}
        Survey description: {$survey->description}

        You are provided with survey responses in a CSV format - first row contains the headers and subsequent rows contain the responses.

        ```csv
        $csvContent
        ```

        Please analyze the responses and provide a summary that highlights the overall sentiment of the survey, either positive, neutral or negative, and a concise summary of all responses in the data.

        Response format should be a parsable JSON object (remove any annotations that can't be parsed) with the following structure:

        {
            "sentiment": "positive|neutral|negative",
            "summary": "Concise summary of the survey responses."
        }

        PROMPT;
    }

    private function getSystemPrompt(): string
    {
        return <<<PROMPT
        You are an expert in summarizing survey data. Your task is to generate a concise summary of the survey responses provided to you. The summary should highlight the overall sentiment of the survey, either positive, neutral or negative, and a concise summary of all responses in the data.
        Please ensure that the summary is clear, concise, and provides actionable insights based on the responses. Focus on the most significant findings and avoid unnecessary details.
        PROMPT;
    }
}
// phpcs:enable Generic.Files.LineLength
