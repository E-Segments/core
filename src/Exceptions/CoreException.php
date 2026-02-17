<?php

declare(strict_types=1);

namespace Esegments\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Base exception class for Esegments packages.
 *
 * Provides consistent error formatting and HTTP response rendering.
 */
class CoreException extends Exception
{
    /**
     * The error code for API responses.
     */
    protected ?string $errorCode = null;

    /**
     * Additional context data for the error.
     *
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * HTTP status code for the response.
     */
    protected int $statusCode = 500;

    /**
     * Create a new exception instance.
     *
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?string $errorCode = null,
        array $context = [],
    ) {
        parent::__construct($message, $code, $previous);

        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    /**
     * Create a new exception with context.
     *
     * @param  array<string, mixed>  $context
     */
    public static function withContext(string $message, array $context = []): static
    {
        return new static($message, 0, null, null, $context);
    }

    /**
     * Get the error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Set the error code.
     */
    public function setErrorCode(string $errorCode): static
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get the context data.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Add context data.
     *
     * @param  array<string, mixed>  $context
     */
    public function addContext(array $context): static
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set the HTTP status code.
     */
    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $this->getMessage(),
        ];

        if ($this->errorCode !== null) {
            $data['code'] = $this->errorCode;
        }

        if (! empty($this->context) && config('app.debug')) {
            $data['context'] = $this->context;
        }

        return response()->json($data, $this->statusCode);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Override in subclasses if needed
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->errorCode,
            'context' => $this->context,
            'status_code' => $this->statusCode,
        ];
    }
}
