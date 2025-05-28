<?php

declare(strict_types=1);

namespace App\Domains\Surveys;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SurveyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCommands();
        $this->registerRoutes();
    }

    private function registerCommands(): void
    {
        $commands = [
            $this->app->basePath('app/Domains/Surveys/Commands'),
            $this->app->basePath('app/Domains/Surveys/Routes/console.php'),
        ];

        [$commands, $paths] = (new Collection($commands))->partition(fn($command) => class_exists($command));
        [$routes, $paths] = $paths->partition(fn($path) => is_file($path));

        /** @var ConsoleKernel $kernel */
        $kernel = resolve(Kernel::class);

        $kernel->addCommands($commands->all());
        $kernel->addCommandPaths($paths->all());
        $kernel->addCommandRoutePaths($routes->all());
    }

    private function registerRoutes(): void
    {
        Route::prefix('api')
            ->middleware(['api'])
            ->group(__DIR__ . '/Routes/api.php');
    }
}
