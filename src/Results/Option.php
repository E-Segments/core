<?php

declare(strict_types=1);

namespace Esegments\Core\Results;

use Esegments\Core\Contracts\Arrayable;
use JsonSerializable;

/**
 * Option type for handling nullable values safely.
 *
 * @template T The type of the wrapped value
 *
 * @implements Arrayable<string, mixed>
 */
final class Option implements Arrayable, JsonSerializable
{
    /**
     * @param  T|null  $value
     */
    private function __construct(
        private readonly mixed $value,
        private readonly bool $hasValue,
    ) {}

    /**
     * Create a Some option with a value.
     *
     * @template TValue
     *
     * @param  TValue  $value
     * @return Option<TValue>
     */
    public static function some(mixed $value): self
    {
        return new self($value, true);
    }

    /**
     * Create a None option (no value).
     *
     * @return Option<never>
     */
    public static function none(): self
    {
        return new self(null, false);
    }

    /**
     * Create an Option from a nullable value.
     *
     * @template TValue
     *
     * @param  TValue|null  $value
     * @return Option<TValue>
     */
    public static function fromNullable(mixed $value): self
    {
        return $value !== null ? self::some($value) : self::none();
    }

    /**
     * Check if the option has a value.
     */
    public function isSome(): bool
    {
        return $this->hasValue;
    }

    /**
     * Check if the option has no value.
     */
    public function isNone(): bool
    {
        return ! $this->hasValue;
    }

    /**
     * Get the value or throw an exception.
     *
     * @return T
     *
     * @throws OptionException
     */
    public function getOrThrow(): mixed
    {
        if (! $this->hasValue) {
            throw new OptionException('Cannot get value from None option');
        }

        return $this->value;
    }

    /**
     * Get the value or return a default.
     *
     * @template TDefault
     *
     * @param  TDefault  $default
     * @return T|TDefault
     */
    public function getOrDefault(mixed $default): mixed
    {
        return $this->hasValue ? $this->value : $default;
    }

    /**
     * Get the value or execute a callback.
     *
     * @template TDefault
     *
     * @param  callable(): TDefault  $callback
     * @return T|TDefault
     */
    public function getOrElse(callable $callback): mixed
    {
        return $this->hasValue ? $this->value : $callback();
    }

    /**
     * Get the value or null.
     *
     * @return T|null
     */
    public function getOrNull(): mixed
    {
        return $this->value;
    }

    /**
     * Map the value to a new value.
     *
     * @template TNew
     *
     * @param  callable(T): TNew  $mapper
     * @return Option<TNew>
     */
    public function map(callable $mapper): self
    {
        if (! $this->hasValue) {
            return self::none();
        }

        return self::some($mapper($this->value));
    }

    /**
     * FlatMap the value to a new Option.
     *
     * @template TNew
     *
     * @param  callable(T): Option<TNew>  $mapper
     * @return Option<TNew>
     */
    public function flatMap(callable $mapper): self
    {
        if (! $this->hasValue) {
            return self::none();
        }

        return $mapper($this->value);
    }

    /**
     * Filter the option based on a predicate.
     *
     * @param  callable(T): bool  $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): self
    {
        if (! $this->hasValue || ! $predicate($this->value)) {
            return self::none();
        }

        return $this;
    }

    /**
     * Execute a callback if the option has a value.
     *
     * @param  callable(T): void  $callback
     */
    public function ifSome(callable $callback): self
    {
        if ($this->hasValue) {
            $callback($this->value);
        }

        return $this;
    }

    /**
     * Execute a callback if the option has no value.
     *
     * @param  callable(): void  $callback
     */
    public function ifNone(callable $callback): self
    {
        if (! $this->hasValue) {
            $callback();
        }

        return $this;
    }

    /**
     * Match on some or none.
     *
     * @template TSome
     * @template TNone
     *
     * @param  callable(T): TSome  $onSome
     * @param  callable(): TNone  $onNone
     * @return TSome|TNone
     */
    public function match(callable $onSome, callable $onNone): mixed
    {
        return $this->hasValue ? $onSome($this->value) : $onNone();
    }

    /**
     * Convert to a Result.
     *
     * @return Result<T>
     */
    public function toResult(string $noneMessage = 'No value present'): Result
    {
        return $this->hasValue
            ? Result::success($this->value)
            : Result::failure($noneMessage);
    }

    /**
     * @return array{has_value: bool, value: T|null}
     */
    public function toArray(): array
    {
        return [
            'has_value' => $this->hasValue,
            'value' => $this->value,
        ];
    }

    /**
     * @return array{has_value: bool, value: T|null}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
