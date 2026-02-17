---
title: "Traits"
description: "Reusable behaviors for your classes"
order: 4
---

Esegments Core provides several reusable traits for common functionality.

## Makeable

Adds a static `make()` factory method to your class:

```php
use Esegments\Core\Concerns\Makeable;

class OrderDTO
{
    use Makeable;

    public function __construct(
        public int $customerId,
        public array $items,
        public ?string $note = null
    ) {}
}

// Usage
$dto = OrderDTO::make(
    customerId: 123,
    items: [['product_id' => 1, 'quantity' => 2]],
    note: 'Rush delivery'
);
```

### Named Constructor Pattern

Combine with static methods:

```php
class Money
{
    use Makeable;

    private function __construct(
        public int $cents,
        public string $currency
    ) {}

    public static function usd(float $dollars): self
    {
        return self::make(
            cents: (int) ($dollars * 100),
            currency: 'USD'
        );
    }

    public static function fromCents(int $cents, string $currency = 'USD'): self
    {
        return self::make(cents: $cents, currency: $currency);
    }
}

$money = Money::usd(19.99);
```

## HasUuid

Automatically generates UUIDs for Eloquent models:

```php
use Esegments\Core\Concerns\HasUuid;

class Order extends Model
{
    use HasUuid;

    // UUID is automatically set on creation
}

$order = Order::create(['customer_id' => 1]);
echo $order->uuid; // "550e8400-e29b-41d4-a716-446655440000"
```

### Custom UUID Column

```php
class Order extends Model
{
    use HasUuid;

    protected string $uuidColumn = 'public_id';
}
```

### Route Model Binding

```php
// Routes
Route::get('/orders/{order:uuid}', [OrderController::class, 'show']);

// Or set as route key
class Order extends Model
{
    use HasUuid;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
```

## BootableTrait

Automatically calls boot methods for traits:

```php
use Esegments\Core\Concerns\BootableTrait;

trait LogsChanges
{
    public static function bootLogsChanges(): void
    {
        static::updated(function ($model) {
            Log::info("Model updated", [
                'model' => get_class($model),
                'id' => $model->id,
                'changes' => $model->getChanges(),
            ]);
        });
    }
}

class Order extends Model
{
    use BootableTrait, LogsChanges;

    // bootLogsChanges() is automatically called
}
```

## ConfigurableTrait

Adds configuration handling to your classes:

```php
use Esegments\Core\Concerns\ConfigurableTrait;
use Esegments\Core\Contracts\Configurable;

class PaymentGateway implements Configurable
{
    use ConfigurableTrait;

    protected array $defaultConfig = [
        'timeout' => 30,
        'retry' => 3,
        'sandbox' => false,
    ];
}

// Usage
$gateway = new PaymentGateway();
$gateway->configure(['sandbox' => true]);

$timeout = $gateway->getConfig('timeout'); // 30
$sandbox = $gateway->getConfig('sandbox'); // true
$all = $gateway->getConfig();              // full config array
```

### With Validation

```php
class PaymentGateway implements Configurable
{
    use ConfigurableTrait;

    protected function validateConfig(array $config): void
    {
        if (isset($config['timeout']) && $config['timeout'] < 1) {
            throw new ConfigurationException('Timeout must be positive');
        }
    }
}
```

## ForwardsCalls

Forward method calls to another object:

```php
use Esegments\Core\Concerns\ForwardsCalls;

class UserDecorator
{
    use ForwardsCalls;

    public function __construct(
        protected User $user
    ) {}

    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->user, $method, $parameters);
    }

    public function displayName(): string
    {
        return "{$this->user->name} ({$this->user->email})";
    }
}

$decorated = new UserDecorator($user);
$decorated->displayName();  // Custom method
$decorated->save();         // Forwarded to User
```

### Conditional Forwarding

```php
class CachedRepository
{
    use ForwardsCalls;

    public function __construct(
        protected Repository $repository,
        protected Cache $cache
    ) {}

    public function __call(string $method, array $parameters)
    {
        // Cache read methods
        if (str_starts_with($method, 'find') || str_starts_with($method, 'get')) {
            $key = $method . ':' . serialize($parameters);

            return $this->cache->remember($key, 3600, function () use ($method, $parameters) {
                return $this->forwardCallTo($this->repository, $method, $parameters);
            });
        }

        // Forward writes directly
        return $this->forwardCallTo($this->repository, $method, $parameters);
    }
}
```

## Enum Concerns

### HasLabel

```php
use Esegments\Core\Enums\Concerns\HasLabel;

enum OrderStatus: string
{
    use HasLabel;

    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Review',
            self::Processing => 'Being Processed',
            self::Shipped => 'Shipped to Customer',
        };
    }
}

echo OrderStatus::Pending->label(); // "Pending Review"
```

### HasColor

```php
use Esegments\Core\Enums\Concerns\HasColor;

enum OrderStatus: string
{
    use HasColor;

    case Pending = 'pending';
    case Shipped = 'shipped';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Shipped => 'success',
        };
    }
}
```

### HasIcon

```php
use Esegments\Core\Enums\Concerns\HasIcon;

enum OrderStatus: string
{
    use HasIcon;

    case Pending = 'pending';
    case Shipped = 'shipped';

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Shipped => 'heroicon-o-truck',
        };
    }
}
```

### HasDescription

```php
use Esegments\Core\Enums\Concerns\HasDescription;

enum PaymentMethod: string
{
    use HasDescription;

    case CreditCard = 'credit_card';
    case BankTransfer = 'bank_transfer';

    public function description(): string
    {
        return match ($this) {
            self::CreditCard => 'Pay securely with Visa, Mastercard, or Amex',
            self::BankTransfer => 'Direct transfer from your bank account',
        };
    }
}
```

### Combined for Filament

```php
use Esegments\Core\Enums\Concerns\{HasLabel, HasColor, HasIcon};
use Filament\Support\Contracts\{HasLabel as FilamentHasLabel, HasColor as FilamentHasColor, HasIcon as FilamentHasIcon};

enum OrderStatus: string implements FilamentHasLabel, FilamentHasColor, FilamentHasIcon
{
    use HasLabel, HasColor, HasIcon;

    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return $this->color();
    }

    public function getIcon(): string
    {
        return $this->icon();
    }

    public function label(): string { /* ... */ }
    public function color(): string { /* ... */ }
    public function icon(): string { /* ... */ }
}
```
