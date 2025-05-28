<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Tests\Unit\Jobs;

use App\Domains\Surveys\Actions\GenerateSurveySummaryAction;
use App\Domains\Surveys\Jobs\GenerateSurveySummaryJob;
use App\Domains\Surveys\Models\SurveySummary;
use Tests\TestCase;

describe('GenerateSurveySummaryJob', function (): void {
    it('calls execute on GenerateSurveySummaryAction with the survey summary', function () {
        /** @var TestCase $this */
        $summary = SurveySummary::factory()->create();

        $action = $this->mock(GenerateSurveySummaryAction::class);
        $action
            ->shouldReceive('execute')
            ->once()
            ->with($summary);

        $job = new GenerateSurveySummaryJob($summary);
        $job->handle($action);
    });
});
