<?php

declare(strict_types=1);

namespace Esegments\Core\Contracts;

/**
 * Contract for classes that need initialization after construction.
 *
 * Useful for service providers, modules, or any class that needs
 * a boot phase separate from construction.
 */
interface Bootable
{
    /**
     * Boot the instance.
     *
     * This method is called after the class is constructed and
     * all dependencies are resolved.
     */
    public function boot(): void;

    /**
     * Check if the instance has been booted.
     */
    public function isBooted(): bool;
}
