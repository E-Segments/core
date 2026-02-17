<?php

declare(strict_types=1);

namespace Esegments\Core\Concerns;

use BadMethodCallException;
use Error;

/**
 * Trait for forwarding method calls to another object.
 */
trait ForwardsCalls
{
    /**
     * Forward a method call to the given object.
     *
     * @param  mixed  $object
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    protected function forwardCallTo(mixed $object, string $method, array $parameters): mixed
    {
        try {
            return $object->{$method}(...$parameters);
        } catch (Error|BadMethodCallException $e) {
            $pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';

            if (! preg_match($pattern, $e->getMessage(), $matches)) {
                throw $e;
            }

            if ($matches['class'] !== $object::class || $matches['method'] !== $method) {
                throw $e;
            }

            static::throwBadMethodCallException($method);
        }
    }

    /**
     * Forward a method call to the given object, returning $this if the forwarded call returned itself.
     *
     * @param  mixed  $object
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    protected function forwardDecoratedCallTo(mixed $object, string $method, array $parameters): mixed
    {
        $result = $this->forwardCallTo($object, $method, $parameters);

        if ($result === $object) {
            return $this;
        }

        return $result;
    }

    /**
     * Throw a bad method call exception for the given method.
     *
     * @throws BadMethodCallException
     */
    protected static function throwBadMethodCallException(string $method): never
    {
        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            static::class,
            $method
        ));
    }
}
