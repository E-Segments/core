<?php

declare(strict_types=1);

namespace Esegments\Core\Tests;

use Esegments\Core\Results\Failure;
use Esegments\Core\Results\Option;
use Esegments\Core\Results\OptionException;
use Esegments\Core\Results\Success;

final class OptionTest extends TestCase
{
    public function test_some_creation(): void
    {
        $option = Option::some('value');

        $this->assertTrue($option->isSome());
        $this->assertFalse($option->isNone());
    }

    public function test_none_creation(): void
    {
        $option = Option::none();

        $this->assertTrue($option->isNone());
        $this->assertFalse($option->isSome());
    }

    public function test_from_nullable_with_value(): void
    {
        $option = Option::fromNullable('value');

        $this->assertTrue($option->isSome());
        $this->assertEquals('value', $option->getOrThrow());
    }

    public function test_from_nullable_with_null(): void
    {
        $option = Option::fromNullable(null);

        $this->assertTrue($option->isNone());
    }

    public function test_get_or_throw_on_some(): void
    {
        $option = Option::some('value');

        $this->assertEquals('value', $option->getOrThrow());
    }

    public function test_get_or_throw_on_none(): void
    {
        $this->expectException(OptionException::class);

        $option = Option::none();
        $option->getOrThrow();
    }

    public function test_get_or_default_on_some(): void
    {
        $option = Option::some('value');

        $this->assertEquals('value', $option->getOrDefault('default'));
    }

    public function test_get_or_default_on_none(): void
    {
        $option = Option::none();

        $this->assertEquals('default', $option->getOrDefault('default'));
    }

    public function test_get_or_else_on_some(): void
    {
        $option = Option::some('value');

        $this->assertEquals('value', $option->getOrElse(fn () => 'fallback'));
    }

    public function test_get_or_else_on_none(): void
    {
        $option = Option::none();

        $this->assertEquals('fallback', $option->getOrElse(fn () => 'fallback'));
    }

    public function test_get_or_null_on_some(): void
    {
        $option = Option::some('value');

        $this->assertEquals('value', $option->getOrNull());
    }

    public function test_get_or_null_on_none(): void
    {
        $option = Option::none();

        $this->assertNull($option->getOrNull());
    }

    public function test_map_on_some(): void
    {
        $option = Option::some(5)
            ->map(fn ($value) => $value * 2);

        $this->assertTrue($option->isSome());
        $this->assertEquals(10, $option->getOrThrow());
    }

    public function test_map_on_none(): void
    {
        $option = Option::none()
            ->map(fn ($value) => $value * 2);

        $this->assertTrue($option->isNone());
    }

    public function test_flat_map_on_some(): void
    {
        $option = Option::some(5)
            ->flatMap(fn ($value) => Option::some($value * 2));

        $this->assertTrue($option->isSome());
        $this->assertEquals(10, $option->getOrThrow());
    }

    public function test_flat_map_on_none(): void
    {
        $option = Option::none()
            ->flatMap(fn ($value) => Option::some($value * 2));

        $this->assertTrue($option->isNone());
    }

    public function test_filter_passes(): void
    {
        $option = Option::some(10)
            ->filter(fn ($value) => $value > 5);

        $this->assertTrue($option->isSome());
    }

    public function test_filter_fails(): void
    {
        $option = Option::some(3)
            ->filter(fn ($value) => $value > 5);

        $this->assertTrue($option->isNone());
    }

    public function test_filter_on_none(): void
    {
        $option = Option::none()
            ->filter(fn ($value) => true);

        $this->assertTrue($option->isNone());
    }

    public function test_if_some_called(): void
    {
        $called = false;
        $option = Option::some('value');

        $option->ifSome(function ($value) use (&$called): void {
            $called = true;
            $this->assertEquals('value', $value);
        });

        $this->assertTrue($called);
    }

    public function test_if_some_not_called_on_none(): void
    {
        $called = false;
        $option = Option::none();

        $option->ifSome(function () use (&$called): void {
            $called = true;
        });

        $this->assertFalse($called);
    }

    public function test_if_none_called(): void
    {
        $called = false;
        $option = Option::none();

        $option->ifNone(function () use (&$called): void {
            $called = true;
        });

        $this->assertTrue($called);
    }

    public function test_if_none_not_called_on_some(): void
    {
        $called = false;
        $option = Option::some('value');

        $option->ifNone(function () use (&$called): void {
            $called = true;
        });

        $this->assertFalse($called);
    }

    public function test_match_on_some(): void
    {
        $option = Option::some('value');

        $matched = $option->match(
            fn ($value) => "Some: {$value}",
            fn () => 'None'
        );

        $this->assertEquals('Some: value', $matched);
    }

    public function test_match_on_none(): void
    {
        $option = Option::none();

        $matched = $option->match(
            fn ($value) => "Some: {$value}",
            fn () => 'None'
        );

        $this->assertEquals('None', $matched);
    }

    public function test_to_result_on_some(): void
    {
        $option = Option::some('value');
        $result = $option->toResult();

        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals('value', $result->getOrThrow());
    }

    public function test_to_result_on_none(): void
    {
        $option = Option::none();
        $result = $option->toResult('Custom message');

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('Custom message', $result->getMessage());
    }

    public function test_to_array(): void
    {
        $some = Option::some('value');
        $none = Option::none();

        $this->assertEquals(['has_value' => true, 'value' => 'value'], $some->toArray());
        $this->assertEquals(['has_value' => false, 'value' => null], $none->toArray());
    }

    public function test_json_serialization(): void
    {
        $option = Option::some('value');

        $this->assertJson(json_encode($option));
    }
}
