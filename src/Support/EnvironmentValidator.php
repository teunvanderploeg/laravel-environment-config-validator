<?php

declare(strict_types=1);

namespace Teun\LaravelEnvironmentConfigValidator\Support;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Str;
use InvalidArgumentException;

class EnvironmentValidator
{
    public function __construct(private readonly ValidationFactory $validationFactory) {}

    /**
     * @param  array<string, mixed>  $rules
     * @return array{ok: bool, errors: array<string, array<int, string>>}
     */
    public function validate(array $rules, ?string $envFilePath = null): array
    {
        $data = $this->resolveValidationData($rules, $envFilePath);

        $validator = $this->validationFactory->make($data, $rules);

        return [
            'ok' => ! $validator->fails(),
            'errors' => $validator->errors()->toArray(),
        ];
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     */
    private function resolveValidationData(array $rules, ?string $envFilePath): array
    {
        $values = [];

        if ($envFilePath === null) {
            foreach (array_keys($rules) as $key) {
                $values[$key] = $this->resolveEnvironmentValue((string) $key);
            }

            return $values;
        }

        $fileValues = $this->parseEnvFile($envFilePath);

        foreach (array_keys($rules) as $key) {
            $values[$key] = $fileValues[$key] ?? null;
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseEnvFile(string $path): array
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException(sprintf('Environment file not found: %s', $path));
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Failed to read environment file: %s', $path));
        }

        $values = [];

        foreach (preg_split("/\r\n|\n|\r/", $contents) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || Str::startsWith($line, '#')) {
                continue;
            }

            $line = preg_replace('/^export\s+/', '', $line) ?? $line;

            if (! str_contains($line, '=')) {
                continue;
            }

            [$key, $rawValue] = explode('=', $line, 2);
            $key = trim($key);

            if ($key === '') {
                continue;
            }

            $values[$key] = $this->normalizeEnvValue(trim($rawValue));
        }

        return $values;
    }

    private function normalizeEnvValue(string $value): mixed
    {
        if ($value === '') {
            return '';
        }

        $first = $value[0];
        $last = $value[strlen($value) - 1];
        $isQuoted = ($first === '"' && $last === '"') || ($first === "'" && $last === "'");

        if (! $isQuoted) {
            $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
            $value = trim($value);
        } else {
            $value = substr($value, 1, -1);
            if ($first === '"') {
                $value = stripcslashes($value);
            }
        }

        $normalized = strtolower($value);

        return match ($normalized) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }

    /**
     * @param  array<int, string>  $requiredKeys
     * @return array<int, string>
     */
    public function compareWithEnvExample(array $requiredKeys, string $path): array
    {
        if (! is_file($path)) {
            return ['.env.example not found; skipping example comparison'];
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return ['Failed to read .env.example; skipping example comparison'];
        }

        $exampleKeys = $this->parseEnvExampleKeys($contents);
        $missingInExample = array_values(array_diff($requiredKeys, $exampleKeys));

        return array_map(
            static fn (string $key): string => sprintf(
                '%s is in rules but missing from .env.example',
                $key
            ),
            $missingInExample
        );
    }

    private function resolveEnvironmentValue(string $key): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        $value = getenv($key);

        return $value === false ? null : $value;
    }

    /**
     * @return array<int, string>
     */
    private function parseEnvExampleKeys(string $contents): array
    {
        $keys = [];

        foreach (preg_split("/\r\n|\n|\r/", $contents) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || Str::startsWith($line, '#')) {
                continue;
            }

            $line = preg_replace('/^export\s+/', '', $line) ?? $line;

            if (! str_contains($line, '=')) {
                continue;
            }

            [$key] = explode('=', $line, 2);
            $key = trim($key);

            if ($key !== '') {
                $keys[] = $key;
            }
        }

        return array_values(array_unique($keys));
    }
}
