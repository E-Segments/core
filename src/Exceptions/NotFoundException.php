<?php

declare(strict_types=1);

namespace Esegments\Core\Exceptions;

/**
 * Exception for resource not found errors.
 */
class NotFoundException extends CoreException
{
    protected int $statusCode = 404;

    protected ?string $errorCode = 'NOT_FOUND';

    /**
     * Create a new not found exception for a model.
     */
    public static function forModel(string $model, string|int $id): static
    {
        $modelName = class_basename($model);

        return new static(
            message: "{$modelName} with ID {$id} not found.",
            errorCode: strtoupper($modelName).'_NOT_FOUND',
            context: ['model' => $model, 'id' => $id],
        );
    }

    /**
     * Create a new not found exception for a resource.
     */
    public static function forResource(string $resource): static
    {
        return new static(
            message: "{$resource} not found.",
            errorCode: 'RESOURCE_NOT_FOUND',
            context: ['resource' => $resource],
        );
    }
}
