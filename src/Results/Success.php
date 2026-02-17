<?php

declare(strict_types=1);

namespace Esegments\Core\Results;

/**
 * Represents a successful result.
 *
 * @template T The type of the success value
 *
 * @extends Result<T>
 */
final class Success extends Result
{
    /**
     * @param  T  $value
     */
    public function __construct(
        private readonly mixed $value = null,
    ) {}

    /**
     * Get the success value.
     *
     * @return T
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function isSuccess(): bool
    {
        return true;
    }

    public function isFailure(): bool
    {
        return false;
    }

    /**
     * @return T
     */
    public function getOrThrow(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function getOrDefault(mixed $default): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function getOrElse(callable $callback): mixed
    {
        return $this->value;
    }

    public function map(callable $mapper): Result
    {
        return Result::try(fn () => $mapper($this->value));
    }

    public function flatMap(callable $mapper): Result
    {
        try {
            return $mapper($this->value);
        } catch (\Throwable $exception) {
            return Result::fromException($exception);
        }
    }

    public function onSuccess(callable $callback): static
    {
        $callback($this->value);

        return $this;
    }

    public function onFailure(callable $callback): static
    {
        return $this;
    }

    public function match(callable $onSuccess, callable $onFailure): mixed
    {
        return $onSuccess($this->value);
    }

    /**
     * @return array{success: true, value: T}
     */
    public function toArray(): array
    {
        return [
            'success' => true,
            'value' => $this->value,
        ];
    }
}
