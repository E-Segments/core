<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Result Pattern Settings
    |--------------------------------------------------------------------------
    |
    | Configure default behavior for the Result pattern.
    |
    */
    'results' => [
        // Include exception traces in failure results (disable in production)
        'include_traces' => env('ESEGMENTS_INCLUDE_TRACES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Settings
    |--------------------------------------------------------------------------
    |
    | Configure default behavior for package exceptions.
    |
    */
    'exceptions' => [
        // Include context in exception responses (disable in production)
        'include_context' => env('ESEGMENTS_INCLUDE_CONTEXT', false),
    ],
];
