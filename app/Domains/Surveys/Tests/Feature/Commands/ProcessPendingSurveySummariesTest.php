<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Feature\Commands;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Jobs\GenerateSurveySummaryJob;
use App\Domains\Surveys\Models\SurveySummary;
use Illuminate\Support\Facades\Queue;

describe('ProcessPendingSurveySummariesCommand', function (): void {
    it('dispatches jobs for pending survey summaries', function () {
        Queue::fake();

        $pending = SurveySummary::factory()->count(2)->create([
            'status' => SurveySummaryStatusEnum::Pending,
        ]);

        SurveySummary::factory()->count(1)->create([
            'status' => SurveySummaryStatusEnum::Completed,
        ]);

        $this->artisan('app:process-pending-survey-summaries')
            ->expectsOutput('Processing pending survey summaries...')
            ->expectsOutput('Total pending survey summaries processed: 2')
            ->assertExitCode(0);

        foreach ($pending as $summary) {
            Queue::assertPushed(GenerateSurveySummaryJob::class, fn ($job) => $job->surveySummary->is($summary));
        }

        Queue::assertPushed(GenerateSurveySummaryJob::class, 2);
    });

    it('outputs no pending survey summaries found if none exist', function () {
        Queue::fake();

        $this->artisan('app:process-pending-survey-summaries')
            ->expectsOutput('Processing pending survey summaries...')
            ->expectsOutput('No pending survey summaries found.')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    });
});
