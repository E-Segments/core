<?php

declare(strict_types=1);

namespace Esegments\Core\Exceptions;

/**
 * Exception for configuration errors.
 */
class ConfigurationException extends CoreException
{
    protected ?string $errorCode = 'CONFIGURATION_ERROR';

    /**
     * Create an exception for a missing configuration key.
     */
    public static function missingKey(string $key): static
    {
        return new static(
            message: "Missing required configuration key: {$key}",
            errorCode: 'MISSING_CONFIG_KEY',
            context: ['key' => $key],
        );
    }

    /**
     * Create an exception for an invalid configuration value.
     */
    public static function invalidValue(string $key, mixed $value, string $expected): static
    {
        return new static(
            message: "Invalid configuration value for '{$key}'. Expected {$expected}.",
            errorCode: 'INVALID_CONFIG_VALUE',
            context: ['key' => $key, 'value' => $value, 'expected' => $expected],
        );
    }

    /**
     * Create an exception for missing environment variable.
     */
    public static function missingEnv(string $variable): static
    {
        return new static(
            message: "Missing required environment variable: {$variable}",
            errorCode: 'MISSING_ENV_VAR',
            context: ['variable' => $variable],
        );
    }
}
