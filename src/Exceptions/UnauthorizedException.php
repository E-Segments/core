<?php

declare(strict_types=1);

namespace Esegments\Core\Exceptions;

/**
 * Exception for unauthorized access.
 */
class UnauthorizedException extends CoreException
{
    protected int $statusCode = 403;

    protected ?string $errorCode = 'UNAUTHORIZED';

    /**
     * Create an exception for missing permission.
     */
    public static function forPermission(string $permission): static
    {
        return new static(
            message: "You do not have the required permission: {$permission}",
            errorCode: 'MISSING_PERMISSION',
            context: ['permission' => $permission],
        );
    }

    /**
     * Create an exception for missing role.
     */
    public static function forRole(string $role): static
    {
        return new static(
            message: "You do not have the required role: {$role}",
            errorCode: 'MISSING_ROLE',
            context: ['role' => $role],
        );
    }

    /**
     * Create an exception for unauthenticated access.
     */
    public static function unauthenticated(): static
    {
        $instance = new static('Authentication required.');
        $instance->statusCode = 401;
        $instance->errorCode = 'UNAUTHENTICATED';

        return $instance;
    }
}
