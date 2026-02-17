<?php

declare(strict_types=1);

namespace Esegments\Core\Tests;

use Esegments\Core\Results\Failure;
use Esegments\Core\Results\Result;
use Esegments\Core\Results\ResultException;
use Esegments\Core\Results\Success;
use Exception;

final class ResultTest extends TestCase
{
    public function test_success_creation(): void
    {
        $result = Result::success('value');

        $this->assertInstanceOf(Success::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isFailure());
        $this->assertEquals('value', $result->getValue());
    }

    public function test_success_with_null_value(): void
    {
        $result = Result::success(null);

        $this->assertInstanceOf(Success::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getValue());
    }

    public function test_failure_creation(): void
    {
        $result = Result::failure('Error message', 'ERROR_CODE');

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Error message', $result->getMessage());
        $this->assertEquals('ERROR_CODE', $result->getCode());
    }

    public function test_failure_from_exception(): void
    {
        $exception = new Exception('Test exception', 500);
        $result = Result::fromException($exception);

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('Test exception', $result->getMessage());
        $this->assertEquals('500', $result->getCode());
        $this->assertSame($exception, $result->getException());
    }

    public function test_try_with_success(): void
    {
        $result = Result::try(fn () => 'success value');

        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals('success value', $result->getOrThrow());
    }

    public function test_try_with_exception(): void
    {
        $result = Result::try(function (): void {
            throw new Exception('Test error');
        });

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('Test error', $result->getMessage());
    }

    public function test_get_or_throw_on_success(): void
    {
        $result = Result::success('value');

        $this->assertEquals('value', $result->getOrThrow());
    }

    public function test_get_or_throw_on_failure(): void
    {
        $this->expectException(ResultException::class);
        $this->expectExceptionMessage('Error message');

        $result = Result::failure('Error message');
        $result->getOrThrow();
    }

    public function test_get_or_default_on_success(): void
    {
        $result = Result::success('value');

        $this->assertEquals('value', $result->getOrDefault('default'));
    }

    public function test_get_or_default_on_failure(): void
    {
        $result = Result::failure('Error');

        $this->assertEquals('default', $result->getOrDefault('default'));
    }

    public function test_get_or_else_on_success(): void
    {
        $result = Result::success('value');

        $this->assertEquals('value', $result->getOrElse(fn () => 'fallback'));
    }

    public function test_get_or_else_on_failure(): void
    {
        $result = Result::failure('Error', 'CODE');

        $value = $result->getOrElse(fn (Failure $f) => "Failed: {$f->getCode()}");

        $this->assertEquals('Failed: CODE', $value);
    }

    public function test_map_on_success(): void
    {
        $result = Result::success(5)
            ->map(fn ($value) => $value * 2);

        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals(10, $result->getOrThrow());
    }

    public function test_map_on_failure(): void
    {
        $result = Result::failure('Error')
            ->map(fn ($value) => $value * 2);

        $this->assertInstanceOf(Failure::class, $result);
    }

    public function test_flat_map_on_success(): void
    {
        $result = Result::success(5)
            ->flatMap(fn ($value) => Result::success($value * 2));

        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals(10, $result->getOrThrow());
    }

    public function test_flat_map_on_failure(): void
    {
        $result = Result::failure('Error')
            ->flatMap(fn ($value) => Result::success($value * 2));

        $this->assertInstanceOf(Failure::class, $result);
    }

    public function test_on_success_callback(): void
    {
        $called = false;
        $result = Result::success('value');

        $result->onSuccess(function ($value) use (&$called): void {
            $called = true;
            $this->assertEquals('value', $value);
        });

        $this->assertTrue($called);
    }

    public function test_on_success_not_called_for_failure(): void
    {
        $called = false;
        $result = Result::failure('Error');

        $result->onSuccess(function () use (&$called): void {
            $called = true;
        });

        $this->assertFalse($called);
    }

    public function test_on_failure_callback(): void
    {
        $called = false;
        $result = Result::failure('Error', 'CODE');

        $result->onFailure(function (Failure $failure) use (&$called): void {
            $called = true;
            $this->assertEquals('Error', $failure->getMessage());
        });

        $this->assertTrue($called);
    }

    public function test_on_failure_not_called_for_success(): void
    {
        $called = false;
        $result = Result::success('value');

        $result->onFailure(function () use (&$called): void {
            $called = true;
        });

        $this->assertFalse($called);
    }

    public function test_match_on_success(): void
    {
        $result = Result::success('value');

        $matched = $result->match(
            fn ($value) => "Success: {$value}",
            fn ($failure) => "Failure: {$failure->getMessage()}"
        );

        $this->assertEquals('Success: value', $matched);
    }

    public function test_match_on_failure(): void
    {
        $result = Result::failure('Error');

        $matched = $result->match(
            fn ($value) => "Success: {$value}",
            fn ($failure) => "Failure: {$failure->getMessage()}"
        );

        $this->assertEquals('Failure: Error', $matched);
    }

    public function test_success_to_array(): void
    {
        $result = Result::success('value');
        $array = $result->toArray();

        $this->assertEquals([
            'success' => true,
            'value' => 'value',
        ], $array);
    }

    public function test_failure_to_array(): void
    {
        $result = Result::failure('Error', 'CODE');
        $array = $result->toArray();

        $this->assertEquals([
            'success' => false,
            'message' => 'Error',
            'code' => 'CODE',
        ], $array);
    }

    public function test_json_serialization(): void
    {
        $success = Result::success('value');
        $failure = Result::failure('Error');

        $this->assertJson(json_encode($success));
        $this->assertJson(json_encode($failure));
    }
}
