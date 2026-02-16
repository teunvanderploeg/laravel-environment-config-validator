<?php

declare(strict_types=1);

namespace Teun\LaravelEnvironmentConfigValidator\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Teun\LaravelEnvironmentConfigValidator\Tests\TestCase;

class ValidateEnvCommandTest extends TestCase
{
    public function test_it_fails_when_no_rules_are_configured(): void
    {
        config()->set('env-validator.preset', 'custom');
        config()->set('env-validator.rules', []);

        $this->artisan('env:validate')
            ->expectsOutput('Invalid environment configuration:')
            ->expectsOutput('- _rules: No rules resolved. Configure a preset or add rules in config/env-validator.php')
            ->assertFailed();
    }

    public function test_it_fails_when_required_environment_value_is_missing(): void
    {
        putenv('APP_NAME');
        unset($_ENV['APP_NAME'], $_SERVER['APP_NAME']);

        config()->set('env-validator.rules', [
            'APP_NAME' => ['required', 'string'],
        ]);
        config()->set('env-validator.preset', 'custom');

        $this->artisan('env:validate')
            ->expectsOutput('Invalid environment configuration:')
            ->expectsOutputToContain('APP_NAME')
            ->assertFailed();
    }

    public function test_it_passes_when_environment_matches_rules(): void
    {
        putenv('APP_NAME=Demo');
        $_ENV['APP_NAME'] = 'Demo';

        config()->set('env-validator.rules', [
            'APP_NAME' => ['required', 'string'],
        ]);
        config()->set('env-validator.preset', 'custom');
        config()->set('env-validator.check_env_example', false);

        $this->artisan('env:validate')
            ->expectsOutputToContain('preset: custom')
            ->assertSuccessful();
    }

    public function test_it_outputs_json_when_requested(): void
    {
        putenv('APP_NAME=Demo');
        $_ENV['APP_NAME'] = 'Demo';

        config()->set('env-validator.rules', [
            'APP_NAME' => ['required', 'string'],
        ]);
        config()->set('env-validator.preset', 'custom');
        config()->set('env-validator.check_env_example', false);

        Artisan::call('env:validate', ['--json' => true]);

        $output = Artisan::output();

        self::assertStringContainsString('"ok": true', $output);
        self::assertStringContainsString('"preset": "custom"', $output);
        self::assertStringContainsString('"warnings": []', $output);
    }

    public function test_it_can_fail_on_missing_env_example_keys_in_strict_mode(): void
    {
        putenv('REQUIRED_FROM_RULES=Demo');
        $_ENV['REQUIRED_FROM_RULES'] = 'Demo';

        config()->set('env-validator.rules', [
            'REQUIRED_FROM_RULES' => ['required', 'string'],
        ]);
        config()->set('env-validator.preset', 'custom');
        config()->set('env-validator.check_env_example', true);

        $this->artisan('env:validate --strict-example')
            ->assertFailed();
    }

    public function test_it_fails_for_unknown_preset(): void
    {
        config()->set('env-validator.preset', 'unknown');
        config()->set('env-validator.rules', []);

        $this->artisan('env:validate')
            ->expectsOutput('Invalid environment configuration:')
            ->expectsOutputToContain('Unknown preset "unknown"')
            ->assertFailed();
    }

    public function test_it_uses_selected_preset_and_allows_overrides(): void
    {
        putenv('APP_ENV=local');
        $_ENV['APP_ENV'] = 'local';

        config()->set('env-validator.preset', 'standard');
        config()->set('env-validator.presets.standard', [
            'APP_ENV' => ['required', 'in:production'],
        ]);
        config()->set('env-validator.rules', [
            'APP_ENV' => ['required', 'in:local,production'],
        ]);
        config()->set('env-validator.check_env_example', false);

        $this->artisan('env:validate')
            ->expectsOutputToContain('preset: standard')
            ->assertSuccessful();
    }

    public function test_it_can_override_preset_from_cli_option(): void
    {
        putenv('STRICT_ONLY=1');
        $_ENV['STRICT_ONLY'] = '1';

        config()->set('env-validator.preset', 'standard');
        config()->set('env-validator.presets.standard', [
            'STANDARD_ONLY' => ['required', 'string'],
        ]);
        config()->set('env-validator.presets.strict', [
            'STRICT_ONLY' => ['required', 'string'],
        ]);
        config()->set('env-validator.rules', []);
        config()->set('env-validator.check_env_example', false);

        $this->artisan('env:validate --preset=strict')
            ->expectsOutputToContain('preset: strict')
            ->assertSuccessful();
    }

    public function test_it_can_validate_a_specific_env_file(): void
    {
        putenv('APP_NAME');
        unset($_ENV['APP_NAME'], $_SERVER['APP_NAME']);

        $path = sys_get_temp_dir().'/env-validator-'.uniqid('', true).'.env.testing';
        file_put_contents($path, "APP_NAME=FromTesting\n");

        config()->set('env-validator.preset', 'custom');
        config()->set('env-validator.rules', [
            'APP_NAME' => ['required', 'string'],
        ]);
        config()->set('env-validator.check_env_example', false);

        try {
            $this->artisan('env:validate', [
                '--env-file' => $path,
            ])
                ->expectsOutputToContain('source:')
                ->assertSuccessful();
        } finally {
            @unlink($path);
        }
    }

    public function test_it_fails_when_the_selected_env_file_does_not_exist(): void
    {
        config()->set('env-validator.preset', 'custom');
        config()->set('env-validator.rules', [
            'APP_NAME' => ['required', 'string'],
        ]);
        config()->set('env-validator.check_env_example', false);

        $missingPath = sys_get_temp_dir().'/env-validator-missing-'.uniqid('', true).'.env.testing';

        $this->artisan('env:validate', [
            '--env-file' => $missingPath,
        ])
            ->expectsOutput('Invalid environment configuration:')
            ->expectsOutputToContain('_env_file')
            ->assertFailed();
    }
}
