<?php

namespace App\Providers;

use App\Http\Contracts\HandleFilesContract;
use App\Http\Services\HandleFilesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HandleFilesContract::class, HandleFilesService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
