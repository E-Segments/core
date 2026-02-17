<?php

declare(strict_types=1);

namespace Esegments\Core\Concerns;

use Illuminate\Support\Arr;

/**
 * Trait implementing the Configurable contract.
 */
trait ConfigurableTrait
{
    /**
     * Configuration values.
     *
     * @var array<string, mixed>
     */
    protected array $configuration = [];

    /**
     * Get a configuration value.
     *
     * @param  string|null  $key  The configuration key (dot notation supported)
     * @param  mixed  $default  Default value if key doesn't exist
     * @return mixed The configuration value
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->configuration;
        }

        return Arr::get($this->configuration, $key, $default);
    }

    /**
     * Set configuration values.
     *
     * @param  array<string, mixed>  $config
     */
    public function configure(array $config): static
    {
        $this->configuration = $config;

        return $this;
    }

    /**
     * Merge configuration values with existing configuration.
     *
     * @param  array<string, mixed>  $config
     */
    public function mergeConfig(array $config): static
    {
        $this->configuration = array_merge($this->configuration, $config);

        return $this;
    }
}
