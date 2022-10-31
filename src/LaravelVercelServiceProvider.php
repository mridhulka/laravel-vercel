<?php

namespace Mridhulka\LaravelVercel;

use Illuminate\Support\ServiceProvider;
use Mridhulka\LaravelVercel\Console\InstallCommand;

class LaravelVercelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../assets/api/index.php' => base_path('api/index.php'),
            __DIR__ . '/../assets/.vercelignore' => base_path('.vercelignore'),
        ], 'assets');
    }
}
