<?php

declare(strict_types=1);

namespace App\Domains\Surveys;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SurveyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        Route::prefix('api')
            ->middleware(['api'])
            ->group(__DIR__ . '/Routes/api.php');
    }
}
