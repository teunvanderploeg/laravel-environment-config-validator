<?php

declare(strict_types=1);

namespace Vrijdag\LaravelEnvironmentConfigValidator;

use Illuminate\Support\ServiceProvider;
use Vrijdag\LaravelEnvironmentConfigValidator\Console\ValidateEnvCommand;

class EnvValidatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/env-validator.php', 'env-validator');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/env-validator.php' => config_path('env-validator.php'),
        ], 'env-validator-config');

        if ($this->app->runningInConsole()) {
            $this->commands([ValidateEnvCommand::class]);
        }
    }
}
