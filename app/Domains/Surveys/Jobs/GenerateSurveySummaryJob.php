<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Jobs;

use App\Domains\Surveys\Actions\GenerateSurveySummaryAction;
use App\Domains\Surveys\Models\SurveySummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSurveySummaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public SurveySummary $surveySummary)
    {
    }

    public function handle(GenerateSurveySummaryAction $action): void
    {
        $action->execute($this->surveySummary);
    }
}
