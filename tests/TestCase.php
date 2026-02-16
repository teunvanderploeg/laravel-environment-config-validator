<?php

declare(strict_types=1);

namespace Teun\LaravelEnvironmentConfigValidator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Teun\LaravelEnvironmentConfigValidator\EnvValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            EnvValidatorServiceProvider::class,
        ];
    }
}
