<?php

declare(strict_types=1);

namespace App\Domains\Surveys;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SurveyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCommands();
        $this->registerRoutes();
    }

    public function boot(): void
    {
        $this->configureRateLimiting();
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
            ->middleware(['web', 'api'])
            ->group(__DIR__ . '/Routes/api.php');

        Route::middleware(['web'])
            ->group(__DIR__ . '/Routes/web.php');
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('participation', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });
    }
}
