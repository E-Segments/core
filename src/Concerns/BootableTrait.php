<?php

declare(strict_types=1);

namespace Esegments\Core\Concerns;

/**
 * Trait implementing the Bootable contract.
 */
trait BootableTrait
{
    /**
     * Whether the instance has been booted.
     */
    private bool $booted = false;

    /**
     * Boot the instance.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->booting();
        $this->booted = true;
        $this->booted();
    }

    /**
     * Check if the instance has been booted.
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * Actions to perform before booting.
     */
    protected function booting(): void
    {
        // Override in implementing class
    }

    /**
     * Actions to perform after booting.
     */
    protected function booted(): void
    {
        // Override in implementing class
    }
}
