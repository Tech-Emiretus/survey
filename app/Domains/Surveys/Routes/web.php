<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Routes;

use Illuminate\Support\Facades\Route;

Route::get('/participate/{id}', fn () => view('vue'));
