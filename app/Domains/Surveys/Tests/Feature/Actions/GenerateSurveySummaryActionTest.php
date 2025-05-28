<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Actions;

use App\Domains\Surveys\Actions\GenerateSurveySummaryAction;
use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Models\Survey;
use App\Domains\Surveys\Models\SurveySummary;
use App\Domains\Surveys\Notifications\SurveySummaryCompleted;
use App\Domains\Surveys\Notifications\SurveySummaryFailed;
use Exception;
use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Resources\Chat;
use OpenAI\Responses\Chat\CreateResponse;

describe('GenerateSurveySummaryAction', function (): void {
    it('throws an exception if the survey is missing', function () {
        Notification::fake();
        OpenAI::fake();

        $survey = Survey::factory()->create();
        $summary = SurveySummary::factory()->create(['survey_id' => $survey->id]);

        // Delete survey to simulate missing survey
        $survey->forceDelete();

        $action = resolve(GenerateSurveySummaryAction::class);
        expect(fn () => $action->execute($summary))
            ->toThrow(Exception::class, 'Survey not found for the provided survey summary.');

        expect($summary)
            ->status->toBe(SurveySummaryStatusEnum::Failed)
            ->error_message->toBe('Survey not found for the provided survey summary.')
            ->completed_at->not->toBeNull();

        Notification::assertSentTo($summary->createdBy, SurveySummaryFailed::class);
        OpenAI::assertNothingSent();
    });

    it('throws an exception if OpenAI fails to generate summary', function () {
        Notification::fake();
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => ''
                        ]
                    ]
                ]
            ])
        ]);

        $survey = Survey::factory()->create();
        $summary = SurveySummary::factory()->create(['survey_id' => $survey->id]);

        $action = resolve(GenerateSurveySummaryAction::class);
        expect(fn() => $action->execute($summary))
            ->toThrow(Exception::class, 'Failed to generate summary from OpenAI.');

        $summary->refresh();

        expect($summary)
            ->status->toBe(SurveySummaryStatusEnum::Failed)
            ->error_message->toBe('Failed to generate summary from OpenAI.')
            ->completed_at->not->toBeNull();

        Notification::assertSentTo($summary->createdBy, SurveySummaryFailed::class);
        OpenAI::assertSent(Chat::class);
    });

   it('throws an exception if OpenAI returns invalid response format', function () {
        Notification::fake();
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode(['sentiment' => '', 'summary' => ''])
                        ]
                    ]
                ]
            ])
        ]);

        $survey = Survey::factory()->create();
        $summary = SurveySummary::factory()->create(['survey_id' => $survey->id]);

        $action = resolve(GenerateSurveySummaryAction::class);
        expect(fn() => $action->execute($summary))
            ->toThrow(Exception::class, 'Invalid response format from OpenAI. Expected sentiment and summary.');

        $summary->refresh();

        expect($summary)
            ->status->toBe(SurveySummaryStatusEnum::Failed)
            ->error_message->toBe('Invalid response format from OpenAI. Expected sentiment and summary.')
            ->completed_at->not->toBeNull();

        Notification::assertSentTo($summary->createdBy, SurveySummaryFailed::class);
        OpenAI::assertSent(Chat::class);
    });

    it('updates the summary and notifies the creator on success', function () {
        Notification::fake();
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode(['sentiment' => 'positive', 'summary' => 'Great feedback!'])
                        ]
                    ]
                ]
            ])
        ]);

        $survey = Survey::factory()->create();
        $summary = SurveySummary::factory()->create(['survey_id' => $survey->id, 'status' => SurveySummaryStatusEnum::Pending]);

        $action = resolve(GenerateSurveySummaryAction::class);
        $action->execute($summary);

        $summary->refresh();

        expect($summary)
            ->status->toBe(SurveySummaryStatusEnum::Completed)
            ->sentiment->toBe('positive')
            ->summary->toBe('Great feedback!')
            ->completed_at->not->toBeNull()
            ->error_message->toBeNull();

        Notification::assertSentTo($summary->createdBy, SurveySummaryCompleted::class);
        OpenAI::assertSent(Chat::class);
    });
});
