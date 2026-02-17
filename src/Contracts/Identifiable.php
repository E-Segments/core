<?php

declare(strict_types=1);

namespace Esegments\Core\Contracts;

/**
 * Contract for classes that have a unique identifier.
 *
 * Provides a standard interface for getting unique identifiers.
 */
interface Identifiable
{
    /**
     * Get the unique identifier.
     */
    public function getId(): string|int;

    /**
     * Get the identifier key name.
     */
    public function getIdKey(): string;
}
