<?php

declare(strict_types=1);

namespace Esegments\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Exception for validation errors.
 */
class ValidationException extends CoreException
{
    protected int $statusCode = 422;

    /**
     * Validation errors.
     *
     * @var array<string, array<string>>
     */
    protected array $errors = [];

    /**
     * Create a new validation exception.
     *
     * @param  array<string, array<string>>  $errors
     */
    public static function withErrors(array $errors, string $message = 'The given data was invalid.'): static
    {
        $instance = new static($message);
        $instance->errors = $errors;
        $instance->errorCode = 'VALIDATION_ERROR';

        return $instance;
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, array<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'code' => $this->errorCode,
            'errors' => $this->errors,
        ], $this->statusCode);
    }
}
