<?php

declare(strict_types=1);

namespace Vrijdag\LaravelEnvironmentConfigValidator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vrijdag\LaravelEnvironmentConfigValidator\EnvValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            EnvValidatorServiceProvider::class,
        ];
    }
}
