<?php

namespace Naveed\BreezeNext;

use Illuminate\Support\ServiceProvider;
use Naveed\BreezeNext\Console\Commands\SetUpCommand;

class BreezeNextServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Controllers/Auth' => app_path('Http/Controllers/Auth'),
            ]);

            $this->commands([
                SetUpCommand::class,
            ]);
        }
    }
}
