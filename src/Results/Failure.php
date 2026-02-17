<?php

declare(strict_types=1);

namespace Esegments\Core\Results;

use Throwable;

/**
 * Represents a failed result.
 *
 * @extends Result<never>
 */
final class Failure extends Result
{
    public function __construct(
        private readonly string $message,
        private readonly ?string $code = null,
        private readonly ?Throwable $exception = null,
    ) {}

    /**
     * Get the error message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the error code.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Get the original exception.
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function isFailure(): bool
    {
        return true;
    }

    /**
     * @throws ResultException
     */
    public function getOrThrow(): never
    {
        throw new ResultException(
            message: $this->message,
            code: (int) ($this->code ?? 0),
            previous: $this->exception,
        );
    }

    public function getOrDefault(mixed $default): mixed
    {
        return $default;
    }

    public function getOrElse(callable $callback): mixed
    {
        return $callback($this);
    }

    public function map(callable $mapper): Result
    {
        return $this;
    }

    public function flatMap(callable $mapper): Result
    {
        return $this;
    }

    public function onSuccess(callable $callback): static
    {
        return $this;
    }

    public function onFailure(callable $callback): static
    {
        $callback($this);

        return $this;
    }

    public function match(callable $onSuccess, callable $onFailure): mixed
    {
        return $onFailure($this);
    }

    /**
     * @return array{success: false, message: string, code: string|null}
     */
    public function toArray(): array
    {
        return [
            'success' => false,
            'message' => $this->message,
            'code' => $this->code,
        ];
    }
}
