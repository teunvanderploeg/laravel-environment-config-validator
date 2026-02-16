<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Preset Selection
    |--------------------------------------------------------------------------
    |
    | Choose which preset to validate against by default.
    | Supported out of the box: standard, strict, custom
    |
    */
    'preset' => 'standard',

    /*
    |--------------------------------------------------------------------------
    | Environment File Source
    |--------------------------------------------------------------------------
    |
    | By default, values are read from the currently loaded runtime environment.
    | Set this to a file path (for example ".env.testing") to validate against
    | that file by default.
    |
    */
    'env_file' => null,

    /*
    |--------------------------------------------------------------------------
    | Built-in Presets
    |--------------------------------------------------------------------------
    |
    | You can edit these, remove them, or add your own custom preset keys.
    | Any rules from `rules` below are applied as overrides on top of the
    | selected preset (except when preset=custom, where only rules are used).
    |
    */
    'presets' => [
        'standard' => [
            'APP_NAME' => ['required', 'string'],
            'APP_ENV' => ['required', 'in:local,production'],
            'APP_KEY' => ['required', 'string'],
            'APP_DEBUG' => ['required', 'boolean'],
            'APP_URL' => ['required', 'url'],
            'APP_LOCALE' => ['required', 'string'],
            'APP_FALLBACK_LOCALE' => ['required', 'string'],
            'APP_FAKER_LOCALE' => ['required', 'string'],

            'LOG_CHANNEL' => ['required', 'string'],
            'LOG_STACK' => ['nullable', 'string'],
            'LOG_DEPRECATIONS_CHANNEL' => ['nullable', 'string'],
            'LOG_LEVEL' => ['required', 'string'],

            'DB_CONNECTION' => ['required', 'string'],
            'DB_HOST' => ['required', 'string'],
            'DB_PORT' => ['required', 'integer'],
            'DB_DATABASE' => ['required', 'string'],
            'DB_USERNAME' => ['required', 'string'],
            'DB_PASSWORD' => ['nullable', 'string'],

            'SESSION_DRIVER' => ['required', 'string'],
            'SESSION_LIFETIME' => ['required', 'integer'],

            'BROADCAST_CONNECTION' => ['required', 'string'],
            'FILESYSTEM_DISK' => ['required', 'string'],
            'QUEUE_CONNECTION' => ['required', 'string'],

            'CACHE_STORE' => ['required', 'string'],
            'CACHE_PREFIX' => ['nullable', 'string'],

            'MEMCACHED_HOST' => ['nullable', 'string'],

            'REDIS_CLIENT' => ['required', 'string'],
            'REDIS_HOST' => ['required', 'string'],
            'REDIS_PASSWORD' => ['nullable', 'string'],
            'REDIS_PORT' => ['required', 'integer'],

            'MAIL_MAILER' => ['required', 'string'],
            'MAIL_SCHEME' => ['nullable', 'string'],
            'MAIL_HOST' => ['required', 'string'],
            'MAIL_PORT' => ['required', 'integer'],
            'MAIL_USERNAME' => ['nullable', 'string'],
            'MAIL_PASSWORD' => ['nullable', 'string'],
            'MAIL_FROM_ADDRESS' => ['required', 'email'],
            'MAIL_FROM_NAME' => ['required', 'string'],

            'AWS_ACCESS_KEY_ID' => ['nullable', 'string'],
            'AWS_SECRET_ACCESS_KEY' => ['nullable', 'string'],
            'AWS_DEFAULT_REGION' => ['nullable', 'string'],
            'AWS_BUCKET' => ['nullable', 'string'],
            'AWS_USE_PATH_STYLE_ENDPOINT' => ['required', 'boolean'],

            'VITE_APP_NAME' => ['required', 'string'],
        ],
        'strict' => [
            'APP_NAME' => ['required', 'string'],
            'APP_ENV' => ['required', 'in:staging,production'],
            'APP_KEY' => ['required', 'string'],
            'APP_DEBUG' => ['required', 'in:false,0'],
            'APP_URL' => ['required', 'url'],
            'APP_LOCALE' => ['required', 'string'],
            'APP_FALLBACK_LOCALE' => ['required', 'string'],
            'APP_FAKER_LOCALE' => ['required', 'string'],

            'LOG_CHANNEL' => ['required', 'string'],
            'LOG_LEVEL' => ['required', 'in:notice,warning,error,critical,alert,emergency'],

            'DB_CONNECTION' => ['required', 'string'],
            'DB_HOST' => ['required', 'string'],
            'DB_PORT' => ['required', 'integer'],
            'DB_DATABASE' => ['required', 'string'],
            'DB_USERNAME' => ['required', 'string'],
            'DB_PASSWORD' => ['required', 'string'],

            'SESSION_DRIVER' => ['required', 'string'],
            'SESSION_LIFETIME' => ['required', 'integer', 'min:1'],
            'SESSION_ENCRYPT' => ['required', 'boolean'],

            'FILESYSTEM_DISK' => ['required', 'string'],
            'QUEUE_CONNECTION' => ['required', 'string'],
            'CACHE_STORE' => ['required', 'string'],

            'REDIS_HOST' => ['required', 'string'],
            'REDIS_PORT' => ['required', 'integer'],

            'MAIL_MAILER' => ['required', 'string'],
            'MAIL_HOST' => ['required', 'string'],
            'MAIL_PORT' => ['required', 'integer'],
            'MAIL_USERNAME' => ['required', 'string'],
            'MAIL_PASSWORD' => ['required', 'string'],
            'MAIL_FROM_ADDRESS' => ['required', 'email'],
            'MAIL_FROM_NAME' => ['required', 'string'],

            'VITE_APP_NAME' => ['required', 'string'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Rule Overrides
    |--------------------------------------------------------------------------
    |
    | These rules override keys from the selected preset.
    | If preset=custom, this array is used as the full rule set.
    |
    */
    'rules' => [],

    /*
    |--------------------------------------------------------------------------
    | Check .env.example Coverage
    |--------------------------------------------------------------------------
    |
    | When enabled, the command reports warnings for keys in `rules` that do
    | not exist in `.env.example`.
    |
    */
    'check_env_example' => true,
];
