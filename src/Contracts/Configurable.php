<?php

declare(strict_types=1);

namespace Esegments\Core\Contracts;

/**
 * Contract for classes that can be configured.
 *
 * Provides a standard interface for getting and setting configuration values.
 */
interface Configurable
{
    /**
     * Get a configuration value.
     *
     * @param  string|null  $key  The configuration key (dot notation supported)
     * @param  mixed  $default  Default value if key doesn't exist
     * @return mixed The configuration value
     */
    public function config(?string $key = null, mixed $default = null): mixed;

    /**
     * Set configuration values.
     *
     * @param  array<string, mixed>  $config
     */
    public function configure(array $config): static;

    /**
     * Merge configuration values with existing configuration.
     *
     * @param  array<string, mixed>  $config
     */
    public function mergeConfig(array $config): static;
}
