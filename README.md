# Esegments Core

Core utilities, contracts, and concerns for Esegments packages.

## Installation

```bash
composer require esegments/core
```

The package will auto-register its service provider.

## Features

- **Result Pattern** - Type-safe error handling without exceptions
- **Option Pattern** - Safe handling of nullable values
- **Core Contracts** - Standard interfaces for bootable, configurable, and identifiable classes
- **Reusable Traits** - Common functionality (Makeable, HasUuid, ForwardsCalls)
- **Enum Concerns** - Filament-compatible enum traits (HasLabel, HasColor, HasIcon)
- **Base Exceptions** - Consistent exception handling with HTTP rendering
- **Data Integration** - Utilities for working with Spatie Laravel Data

## Result Pattern

Handle success/failure states without throwing exceptions:

```php
use Esegments\Core\Results\Result;

// Create results
$success = Result::success($value);
$failure = Result::failure('Something went wrong', 'ERROR_CODE');

// From exceptions
$result = Result::fromException($exception);

// Try pattern
$result = Result::try(fn () => riskyOperation());

// Get value safely
$value = $result->getOrDefault('fallback');
$value = $result->getOrElse(fn ($failure) => handleError($failure));
$value = $result->getOrThrow(); // Throws ResultException on failure

// Transform
$mapped = $result->map(fn ($value) => transform($value));
$chained = $result->flatMap(fn ($value) => anotherResult($value));

// Callbacks
$result
    ->onSuccess(fn ($value) => handleSuccess($value))
    ->onFailure(fn ($failure) => handleFailure($failure));

// Pattern matching
$output = $result->match(
    onSuccess: fn ($value) => "Got: {$value}",
    onFailure: fn ($failure) => "Error: {$failure->getMessage()}"
);
```

## Option Pattern

Handle nullable values safely:

```php
use Esegments\Core\Results\Option;

// Create options
$some = Option::some($value);
$none = Option::none();
$option = Option::fromNullable($maybeNull);

// Get value safely
$value = $option->getOrDefault('fallback');
$value = $option->getOrElse(fn () => computeFallback());
$value = $option->getOrNull(); // Returns null for None
$value = $option->getOrThrow(); // Throws OptionException for None

// Transform
$mapped = $option->map(fn ($value) => transform($value));
$filtered = $option->filter(fn ($value) => $value > 10);

// Callbacks
$option
    ->ifSome(fn ($value) => doSomething($value))
    ->ifNone(fn () => handleEmpty());

// Convert to Result
$result = $option->toResult('No value found');
```

## Core Contracts

### Bootable

For classes that need initialization after construction:

```php
use Esegments\Core\Contracts\Bootable;
use Esegments\Core\Concerns\BootableTrait;

class MyService implements Bootable
{
    use BootableTrait;

    protected function booting(): void
    {
        // Called before boot completes
    }

    protected function booted(): void
    {
        // Called after boot completes
    }
}
```

### Configurable

For classes with configuration:

```php
use Esegments\Core\Contracts\Configurable;
use Esegments\Core\Concerns\ConfigurableTrait;

class MyService implements Configurable
{
    use ConfigurableTrait;
}

$service
    ->configure(['timeout' => 30])
    ->mergeConfig(['retries' => 3]);

$timeout = $service->config('timeout');
$all = $service->config(); // All config
```

## Concerns

### Makeable

Add a static `make()` factory method:

```php
use Esegments\Core\Concerns\Makeable;

class MyClass
{
    use Makeable;

    public function __construct(
        public readonly string $name,
    ) {}
}

$instance = MyClass::make('Example');
```

### HasUuid

For models with UUID identifiers:

```php
use Esegments\Core\Concerns\HasUuid;

class Order extends Model
{
    use HasUuid;

    // Optionally customize the column
    protected static string $uuidColumn = 'uuid';
}

// Find by UUID
$order = Order::findByUuid('550e8400-e29b-41d4-a716-446655440000');
$order = Order::findByUuidOrFail('...');
```

### ForwardsCalls

Forward method calls to another object:

```php
use Esegments\Core\Concerns\ForwardsCalls;

class Decorator
{
    use ForwardsCalls;

    public function __construct(
        private readonly Service $service,
    ) {}

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->service, $method, $parameters);
    }
}
```

## Enum Concerns

Filament-compatible enum traits:

```php
use Esegments\Core\Enums\Concerns\HasLabel;
use Esegments\Core\Enums\Concerns\HasColor;
use Esegments\Core\Enums\Concerns\HasIcon;
use Esegments\Core\Enums\Concerns\HasDescription;

enum OrderStatus: string
{
    use HasLabel, HasColor, HasIcon, HasDescription;

    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';

    private function labels(): array
    {
        return [
            'pending' => 'Pending Review',
            'processing' => 'Being Processed',
            'shipped' => 'Shipped',
        ];
    }

    private function colors(): array
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'success',
        ];
    }

    private function icons(): array
    {
        return [
            'pending' => 'heroicon-o-clock',
            'processing' => 'heroicon-o-cog',
            'shipped' => 'heroicon-o-truck',
        ];
    }
}

// Usage
$status = OrderStatus::Pending;
$status->getLabel();  // "Pending Review"
$status->getColor();  // "warning"
$status->getIcon();   // "heroicon-o-clock"

// Get all
OrderStatus::getLabels();  // ['pending' => 'Pending Review', ...]
```

## Exceptions

Consistent exception handling:

```php
use Esegments\Core\Exceptions\CoreException;
use Esegments\Core\Exceptions\NotFoundException;
use Esegments\Core\Exceptions\UnauthorizedException;
use Esegments\Core\Exceptions\ValidationException;
use Esegments\Core\Exceptions\ConfigurationException;

// Core exception with context
throw CoreException::withContext('Operation failed', [
    'operation' => 'create_order',
    'user_id' => $userId,
]);

// Not found
throw NotFoundException::forModel(Order::class, $orderId);
throw NotFoundException::forResource('Invoice');

// Unauthorized
throw UnauthorizedException::forPermission('manage_orders');
throw UnauthorizedException::forRole('admin');
throw UnauthorizedException::unauthenticated();

// Validation
throw ValidationException::withErrors([
    'email' => ['The email is invalid.'],
    'password' => ['The password is too short.'],
]);

// Configuration
throw ConfigurationException::missingKey('database.connection');
throw ConfigurationException::invalidValue('timeout', 'abc', 'integer');
throw ConfigurationException::missingEnv('DATABASE_URL');
```

All exceptions render as JSON responses with consistent structure:

```json
{
    "success": false,
    "message": "User with ID 123 not found.",
    "code": "USER_NOT_FOUND"
}
```

## Data Integration

Utilities for working with Spatie Laravel Data:

```php
use Esegments\Core\Support\DataFactory;
use App\Data\OrderData;

// From request
$data = DataFactory::fromRequest(OrderData::class, $request);

// From array
$data = DataFactory::fromArray(OrderData::class, ['name' => 'Test']);

// From model
$data = DataFactory::fromModel(OrderData::class, $order);

// Collection
$collection = DataFactory::collection(OrderData::class, $orders);

// Safe creation
$data = DataFactory::tryFrom(OrderData::class, $input); // Returns null on failure
$data = DataFactory::fromOrDefault(OrderData::class, $input, $defaultData);
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=esegments-core-config
```

```php
// config/esegments-core.php
return [
    'results' => [
        'include_traces' => env('ESEGMENTS_INCLUDE_TRACES', false),
    ],
    'exceptions' => [
        'include_context' => env('ESEGMENTS_INCLUDE_CONTEXT', false),
    ],
];
```

## Testing

```bash
cd packages/esegments/core
./vendor/bin/phpunit
```

## License

MIT
