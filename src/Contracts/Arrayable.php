<?php

declare(strict_types=1);

namespace Esegments\Core\Contracts;

/**
 * Contract for classes that can be converted to an array.
 *
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array;
}
