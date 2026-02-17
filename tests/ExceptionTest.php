<?php

declare(strict_types=1);

namespace Esegments\Core\Tests;

use Esegments\Core\Exceptions\ConfigurationException;
use Esegments\Core\Exceptions\CoreException;
use Esegments\Core\Exceptions\NotFoundException;
use Esegments\Core\Exceptions\UnauthorizedException;
use Esegments\Core\Exceptions\ValidationException;
use Exception;

final class ExceptionTest extends TestCase
{
    public function test_core_exception_creation(): void
    {
        $exception = new CoreException(
            message: 'Test error',
            errorCode: 'TEST_ERROR',
            context: ['key' => 'value'],
        );

        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals('TEST_ERROR', $exception->getErrorCode());
        $this->assertEquals(['key' => 'value'], $exception->getContext());
        $this->assertEquals(500, $exception->getStatusCode());
    }

    public function test_core_exception_with_context(): void
    {
        $exception = CoreException::withContext('Error with context', ['data' => 'value']);

        $this->assertEquals('Error with context', $exception->getMessage());
        $this->assertEquals(['data' => 'value'], $exception->getContext());
    }

    public function test_core_exception_fluent_setters(): void
    {
        $exception = new CoreException('Error');

        $exception
            ->setErrorCode('CUSTOM_CODE')
            ->setStatusCode(400)
            ->addContext(['extra' => 'data']);

        $this->assertEquals('CUSTOM_CODE', $exception->getErrorCode());
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals(['extra' => 'data'], $exception->getContext());
    }

    public function test_core_exception_to_array(): void
    {
        $exception = new CoreException(
            message: 'Test error',
            errorCode: 'TEST_ERROR',
            context: ['key' => 'value'],
        );
        $exception->setStatusCode(422);

        $array = $exception->toArray();

        $this->assertEquals('Test error', $array['message']);
        $this->assertEquals('TEST_ERROR', $array['code']);
        $this->assertEquals(['key' => 'value'], $array['context']);
        $this->assertEquals(422, $array['status_code']);
    }

    public function test_validation_exception_with_errors(): void
    {
        $exception = ValidationException::withErrors([
            'email' => ['The email field is required.'],
            'password' => ['The password must be at least 8 characters.'],
        ]);

        $this->assertEquals(422, $exception->getStatusCode());
        $this->assertEquals('VALIDATION_ERROR', $exception->getErrorCode());
        $this->assertArrayHasKey('email', $exception->getErrors());
        $this->assertArrayHasKey('password', $exception->getErrors());
    }

    public function test_not_found_exception_for_model(): void
    {
        $exception = NotFoundException::forModel(\App\Models\User::class, 123);

        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('User with ID 123 not found.', $exception->getMessage());
        $this->assertEquals('USER_NOT_FOUND', $exception->getErrorCode());
        $this->assertEquals(['model' => \App\Models\User::class, 'id' => 123], $exception->getContext());
    }

    public function test_not_found_exception_for_resource(): void
    {
        $exception = NotFoundException::forResource('Order');

        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('Order not found.', $exception->getMessage());
        $this->assertEquals('RESOURCE_NOT_FOUND', $exception->getErrorCode());
    }

    public function test_unauthorized_exception_for_permission(): void
    {
        $exception = UnauthorizedException::forPermission('manage_users');

        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals('MISSING_PERMISSION', $exception->getErrorCode());
        $this->assertStringContainsString('manage_users', $exception->getMessage());
    }

    public function test_unauthorized_exception_for_role(): void
    {
        $exception = UnauthorizedException::forRole('admin');

        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals('MISSING_ROLE', $exception->getErrorCode());
        $this->assertStringContainsString('admin', $exception->getMessage());
    }

    public function test_unauthorized_exception_unauthenticated(): void
    {
        $exception = UnauthorizedException::unauthenticated();

        $this->assertEquals(401, $exception->getStatusCode());
        $this->assertEquals('UNAUTHENTICATED', $exception->getErrorCode());
    }

    public function test_configuration_exception_missing_key(): void
    {
        $exception = ConfigurationException::missingKey('database.connection');

        $this->assertEquals('MISSING_CONFIG_KEY', $exception->getErrorCode());
        $this->assertStringContainsString('database.connection', $exception->getMessage());
    }

    public function test_configuration_exception_invalid_value(): void
    {
        $exception = ConfigurationException::invalidValue('timeout', 'abc', 'integer');

        $this->assertEquals('INVALID_CONFIG_VALUE', $exception->getErrorCode());
        $this->assertStringContainsString('timeout', $exception->getMessage());
    }

    public function test_configuration_exception_missing_env(): void
    {
        $exception = ConfigurationException::missingEnv('DATABASE_URL');

        $this->assertEquals('MISSING_ENV_VAR', $exception->getErrorCode());
        $this->assertStringContainsString('DATABASE_URL', $exception->getMessage());
    }

    public function test_core_exception_with_previous(): void
    {
        $previous = new Exception('Previous error');
        $exception = new CoreException(
            message: 'Current error',
            previous: $previous,
        );

        $this->assertSame($previous, $exception->getPrevious());
    }
}
