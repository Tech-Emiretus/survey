<?php

namespace App\Domains\Surveys\Commands;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Jobs\GenerateSurveySummaryJob;
use App\Domains\Surveys\Models\SurveySummary;
use Illuminate\Console\Command;

class ProcessPendingSurveySummariesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-pending-survey-summaries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending survey summaries';

    public function handle(): int
    {
        $this->comment('Processing pending survey summaries...');

        $total = 0;

        SurveySummary::query()
            ->where('status', SurveySummaryStatusEnum::Pending)
            ->chunk(100, function ($summaries) use (&$total) {
                $total += $summaries->count();

                foreach ($summaries as $summary) {
                    dispatch(new GenerateSurveySummaryJob($summary));
                }
            });

        if ($total === 0) {
            $this->info('No pending survey summaries found.');
        } else {
            $this->info("Total pending survey summaries processed: {$total}");
        }

        return Command::SUCCESS;
    }
}
