<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Routes;

use App\Domains\Surveys\Commands\ProcessPendingSurveySummariesCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ProcessPendingSurveySummariesCommand::class)
    ->withoutOverlapping()
    ->everyMinute();
