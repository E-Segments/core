<?php

declare(strict_types=1);

namespace Esegments\Core\Concerns;

/**
 * Trait for adding a static make() factory method.
 *
 * Useful for fluent construction of objects.
 */
trait Makeable
{
    /**
     * Create a new instance.
     *
     * @param  mixed  ...$arguments
     * @return static
     */
    public static function make(mixed ...$arguments): static
    {
        return new static(...$arguments);
    }
}
