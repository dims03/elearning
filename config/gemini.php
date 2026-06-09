<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API Key and organization. This will be
    | used to authenticate with the Gemini API - you can find your API key
    | on Google AI Studio, at https://aistudio.google.com/app/apikey.
    */

    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Model
    |--------------------------------------------------------------------------
    |
    | Use a model name that is currently available for generateContent.
    | You can override this in your .env file with GEMINI_MODEL.
    |
    */
    'model' => env('GEMINI_MODEL', 'gemini-3.5-flash'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Fallback Models
    |--------------------------------------------------------------------------
    |
    | Comma-separated fallback models that will be tried automatically when
    | the primary model is temporarily unavailable.
    |
    */
    'fallback_models' => array_values(array_filter(array_map(
        static fn (string $model): string => trim($model),
        explode(',', (string) env('GEMINI_FALLBACK_MODELS', 'gemini-2.5-flash,gemini-2.5-flash-lite'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Gemini Base URL
    |--------------------------------------------------------------------------
    |
    | If you need a specific base URL for the Gemini API, you can provide it here.
    | Otherwise, leave empty to use the default value.
    */
    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),

    /*
    |--------------------------------------------------------------------------
    | Gemini API Version
    |--------------------------------------------------------------------------
    |
    | Prefer the stable v1 API for production usage.
    |
    */
    'api_version' => env('GEMINI_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | Number of attempts per model and the initial delay in milliseconds
    | before exponential backoff is applied.
    |
    */
    'retry_attempts' => env('GEMINI_RETRY_ATTEMPTS', 3),
    'retry_delay_ms' => env('GEMINI_RETRY_DELAY_MS', 1000),
];
