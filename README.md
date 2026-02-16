# Laravel Environment Config Validator

Validate Laravel environment variables against rules you define in config.

## Installation

```bash
composer require teun/laravel-environment-config-validator
```

Publish the config file:

```bash
php artisan vendor:publish --tag=env-validator-config
```

## Configuration

Set rules in `config/env-validator.php`:

```php
return [
    'preset' => 'standard', // standard | strict | custom | your-own-preset-key
    'env_file' => null, // null = runtime env, or set '.env.testing'

    'presets' => [
        'standard' => [
            // built-in default Laravel-oriented rules
        ],
        'strict' => [
            // built-in stricter production-focused rules
        ],
        'my-team' => [
            'APP_ENV' => ['required', 'in:staging,production'],
            'DB_PASSWORD' => ['required', 'string'],
        ],
    ],

    // Overrides selected preset keys. If preset=custom, this is the full ruleset.
    'rules' => [
        'APP_ENV' => ['required', 'in:local,staging,production'],
    ],

    'check_env_example' => true,
];
```

## Usage

Run validation:

```bash
php artisan env:validate
```

Override preset at runtime:

```bash
php artisan env:validate --preset=strict
```

Validate a specific file (for example `.env.testing`):

```bash
php artisan env:validate --env-file=.env.testing
```

Machine-readable output:

```bash
php artisan env:validate --json
```

Fail CI when `.env.example` is missing required keys:

```bash
php artisan env:validate --strict-example
```

## CI example

```bash
php artisan env:validate --strict-example
```

Use this command in your deployment or CI pipeline to fail early on invalid environment config.

## Testing

```bash
composer test
```

## License

MIT
