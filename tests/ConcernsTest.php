<?php

declare(strict_types=1);

namespace Esegments\Core\Tests;

use Esegments\Core\Concerns\BootableTrait;
use Esegments\Core\Concerns\ConfigurableTrait;
use Esegments\Core\Concerns\ForwardsCalls;
use Esegments\Core\Concerns\Makeable;
use Esegments\Core\Contracts\Bootable;
use Esegments\Core\Contracts\Configurable;

final class ConcernsTest extends TestCase
{
    public function test_makeable_creates_instance(): void
    {
        $instance = MakeableTestClass::make('value1', 'value2');

        $this->assertInstanceOf(MakeableTestClass::class, $instance);
        $this->assertEquals('value1', $instance->first);
        $this->assertEquals('value2', $instance->second);
    }

    public function test_bootable_boots_once(): void
    {
        $instance = new BootableTestClass;

        $this->assertFalse($instance->isBooted());

        $instance->boot();

        $this->assertTrue($instance->isBooted());
        $this->assertEquals(1, $instance->bootCount);

        // Boot again - should not increment
        $instance->boot();

        $this->assertEquals(1, $instance->bootCount);
    }

    public function test_bootable_calls_booting_and_booted(): void
    {
        $instance = new BootableTestClass;

        $instance->boot();

        $this->assertTrue($instance->bootingCalled);
        $this->assertTrue($instance->bootedCalled);
    }

    public function test_configurable_sets_and_gets_config(): void
    {
        $instance = new ConfigurableTestClass;

        $instance->configure(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertEquals('value1', $instance->config('key1'));
        $this->assertEquals('value2', $instance->config('key2'));
        $this->assertNull($instance->config('nonexistent'));
        $this->assertEquals('default', $instance->config('nonexistent', 'default'));
    }

    public function test_configurable_merges_config(): void
    {
        $instance = new ConfigurableTestClass;

        $instance->configure(['key1' => 'value1']);
        $instance->mergeConfig(['key2' => 'value2']);

        $this->assertEquals('value1', $instance->config('key1'));
        $this->assertEquals('value2', $instance->config('key2'));
    }

    public function test_configurable_returns_all_config(): void
    {
        $instance = new ConfigurableTestClass;

        $config = ['key1' => 'value1', 'key2' => 'value2'];
        $instance->configure($config);

        $this->assertEquals($config, $instance->config());
    }

    public function test_configurable_supports_dot_notation(): void
    {
        $instance = new ConfigurableTestClass;

        $instance->configure([
            'database' => [
                'connection' => 'mysql',
                'host' => 'localhost',
            ],
        ]);

        $this->assertEquals('mysql', $instance->config('database.connection'));
        $this->assertEquals('localhost', $instance->config('database.host'));
    }

    public function test_forwards_calls_to_target(): void
    {
        $target = new ForwardsCallsTarget;
        $instance = new ForwardsCallsTestClass($target);

        $result = $instance->getValue();

        $this->assertEquals('target value', $result);
    }

    public function test_forwards_decorated_calls_returns_self(): void
    {
        $target = new ForwardsCallsTarget;
        $instance = new ForwardsCallsTestClass($target);

        $result = $instance->setValue('new value');

        $this->assertSame($instance, $result);
        $this->assertEquals('new value', $instance->getValue());
    }
}

class MakeableTestClass
{
    use Makeable;

    public function __construct(
        public readonly string $first,
        public readonly string $second,
    ) {}
}

class BootableTestClass implements Bootable
{
    use BootableTrait;

    public int $bootCount = 0;

    public bool $bootingCalled = false;

    public bool $bootedCalled = false;

    protected function booting(): void
    {
        $this->bootingCalled = true;
    }

    protected function booted(): void
    {
        $this->bootCount++;
        $this->bootedCalled = true;
    }
}

class ConfigurableTestClass implements Configurable
{
    use ConfigurableTrait;
}

class ForwardsCallsTarget
{
    private string $value = 'target value';

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}

class ForwardsCallsTestClass
{
    use ForwardsCalls;

    public function __construct(
        private readonly ForwardsCallsTarget $target,
    ) {}

    public function getValue(): string
    {
        return $this->forwardCallTo($this->target, 'getValue', []);
    }

    public function setValue(string $value): self
    {
        return $this->forwardDecoratedCallTo($this->target, 'setValue', [$value]);
    }
}
