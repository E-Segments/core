<?php

declare(strict_types=1);

namespace Esegments\Core\Results;

use Esegments\Core\Contracts\Arrayable;
use JsonSerializable;
use Throwable;

/**
 * Result pattern implementation for handling success/failure states.
 *
 * @template T The type of the success value
 *
 * @implements Arrayable<string, mixed>
 */
abstract class Result implements Arrayable, JsonSerializable
{
    /**
     * Create a success result.
     *
     * @template TValue
     *
     * @param  TValue  $value
     * @return Success<TValue>
     */
    public static function success(mixed $value = null): Success
    {
        return new Success($value);
    }

    /**
     * Create a failure result.
     *
     * @return Failure
     */
    public static function failure(string $message, ?string $code = null, ?Throwable $exception = null): Failure
    {
        return new Failure($message, $code, $exception);
    }

    /**
     * Create a result from a throwable.
     *
     * @return Failure
     */
    public static function fromException(Throwable $exception, ?string $code = null): Failure
    {
        return new Failure(
            message: $exception->getMessage(),
            code: $code ?? (string) $exception->getCode(),
            exception: $exception,
        );
    }

    /**
     * Try to execute a callback and return a result.
     *
     * @template TValue
     *
     * @param  callable(): TValue  $callback
     * @return Result<TValue>
     */
    public static function try(callable $callback): Result
    {
        try {
            return self::success($callback());
        } catch (Throwable $exception) {
            return self::fromException($exception);
        }
    }

    /**
     * Check if the result is a success.
     */
    abstract public function isSuccess(): bool;

    /**
     * Check if the result is a failure.
     */
    abstract public function isFailure(): bool;

    /**
     * Get the success value or throw if failure.
     *
     * @return T
     *
     * @throws ResultException
     */
    abstract public function getOrThrow(): mixed;

    /**
     * Get the success value or return a default.
     *
     * @template TDefault
     *
     * @param  TDefault  $default
     * @return T|TDefault
     */
    abstract public function getOrDefault(mixed $default): mixed;

    /**
     * Get the success value or execute a callback.
     *
     * @template TDefault
     *
     * @param  callable(Failure): TDefault  $callback
     * @return T|TDefault
     */
    abstract public function getOrElse(callable $callback): mixed;

    /**
     * Map the success value to a new value.
     *
     * @template TNew
     *
     * @param  callable(T): TNew  $mapper
     * @return Result<TNew>
     */
    abstract public function map(callable $mapper): Result;

    /**
     * FlatMap the success value to a new Result.
     *
     * @template TNew
     *
     * @param  callable(T): Result<TNew>  $mapper
     * @return Result<TNew>
     */
    abstract public function flatMap(callable $mapper): Result;

    /**
     * Execute a callback if the result is a success.
     *
     * @param  callable(T): void  $callback
     */
    abstract public function onSuccess(callable $callback): static;

    /**
     * Execute a callback if the result is a failure.
     *
     * @param  callable(Failure): void  $callback
     */
    abstract public function onFailure(callable $callback): static;

    /**
     * Match on success or failure.
     *
     * @template TSuccess
     * @template TFailure
     *
     * @param  callable(T): TSuccess  $onSuccess
     * @param  callable(Failure): TFailure  $onFailure
     * @return TSuccess|TFailure
     */
    abstract public function match(callable $onSuccess, callable $onFailure): mixed;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
