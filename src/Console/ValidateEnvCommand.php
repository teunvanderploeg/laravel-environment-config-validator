<?php

declare(strict_types=1);

namespace Vrijdag\LaravelEnvironmentConfigValidator\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Vrijdag\LaravelEnvironmentConfigValidator\Support\EnvironmentValidator;

class ValidateEnvCommand extends Command
{
    protected $signature = 'env:validate {--json : Output machine-readable JSON} {--strict-example : Fail when required keys are missing from .env.example} {--preset= : Override configured preset (standard, strict, custom, or any custom preset key)} {--env-file= : Validate keys against a specific environment file (for example .env.testing)}';

    protected $description = 'Validate environment variables against config/env-validator.php rules';

    public function __construct(private readonly EnvironmentValidator $validator)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        [$rules, $selectedPreset, $presetError] = $this->resolveRules();

        if ($presetError !== null) {
            return $this->outputFailure([
                '_preset' => [$presetError],
            ]);
        }

        if ($rules === []) {
            return $this->outputFailure([
                '_rules' => ['No rules resolved. Configure a preset or add rules in config/env-validator.php'],
            ]);
        }

        $envFilePath = $this->resolveEnvFilePath();

        try {
            $result = $this->validator->validate($rules, $envFilePath);
        } catch (InvalidArgumentException $exception) {
            return $this->outputFailure([
                '_env_file' => [$exception->getMessage()],
            ]);
        }

        if (! $result['ok']) {
            return $this->outputFailure($result['errors']);
        }

        $warnings = [];
        if ((bool) config('env-validator.check_env_example', false) === true) {
            $warnings = $this->validator->compareWithEnvExample(array_keys($rules), base_path('.env.example'));
        }

        if ((bool) $this->option('json')) {
            $this->line((string) json_encode([
                'ok' => true,
                'preset' => $selectedPreset,
                'source' => $envFilePath ?? 'runtime',
                'warnings' => $warnings,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info(sprintf('Environment configuration OK (preset: %s, source: %s)', $selectedPreset, $envFilePath ?? 'runtime'));
            foreach ($warnings as $warning) {
                $this->warn(sprintf('- %s', $warning));
            }
        }

        if ($warnings !== [] && (bool) $this->option('strict-example')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    private function outputFailure(array $errors): int
    {
        if ((bool) $this->option('json')) {
            $this->line((string) json_encode([
                'ok' => false,
                'errors' => $errors,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::FAILURE;
        }

        $this->error('Invalid environment configuration:');

        foreach ($errors as $key => $messages) {
            foreach ($messages as $message) {
                $this->line(sprintf('- %s: %s', $key, $message));
            }
        }

        return self::FAILURE;
    }

    /**
     * @return array{0: array<string, mixed>, 1: string, 2: string|null}
     */
    private function resolveRules(): array
    {
        $configuredPreset = config('env-validator.preset', 'standard');
        $requestedPreset = $this->option('preset');
        $selectedPreset = is_string($requestedPreset) && $requestedPreset !== ''
            ? $requestedPreset
            : (is_string($configuredPreset) ? $configuredPreset : 'standard');

        $selectedPreset = trim($selectedPreset);
        $customRules = config('env-validator.rules', []);
        $customRules = is_array($customRules) ? $customRules : [];

        if ($selectedPreset === 'custom') {
            return [$customRules, 'custom', null];
        }

        $presetRules = config(sprintf('env-validator.presets.%s', $selectedPreset), null);
        if (! is_array($presetRules)) {
            $availablePresets = array_keys((array) config('env-validator.presets', []));
            $availablePresets[] = 'custom';
            $availablePresets = array_values(array_unique($availablePresets));

            return [
                [],
                $selectedPreset,
                sprintf(
                    'Unknown preset "%s". Available presets: %s',
                    $selectedPreset,
                    implode(', ', $availablePresets)
                ),
            ];
        }

        // `rules` always allows project-level overrides on top of the selected preset.
        foreach ($customRules as $key => $rule) {
            $presetRules[$key] = $rule;
        }

        return [$presetRules, $selectedPreset, null];
    }

    private function resolveEnvFilePath(): ?string
    {
        $configuredPath = config('env-validator.env_file');
        $optionPath = $this->option('env-file');

        $selected = is_string($optionPath) && trim($optionPath) !== ''
            ? trim($optionPath)
            : (is_string($configuredPath) && trim($configuredPath) !== '' ? trim($configuredPath) : null);

        if ($selected === null) {
            return null;
        }

        if (str_starts_with($selected, DIRECTORY_SEPARATOR)) {
            return $selected;
        }

        return base_path($selected);
    }
}
