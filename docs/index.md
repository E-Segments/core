---
layout: landing
---

<div class="text-center mb-16">
  <h1 class="text-5xl font-bold mb-6">Esegments Core</h1>
  <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-8">
    Shared foundation utilities for PHP applications. Includes the Result pattern, Option type, reusable traits, contracts, and exceptions.
  </p>
  <div class="flex gap-4 justify-center">
    <a href="/core/docs/getting-started/" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
      Get Started
    </a>
    <a href="https://github.com/E-Segments/core" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
      View on GitHub
    </a>
  </div>
</div>

<div class="not-prose cards-grid mb-16">
  <div class="card">
    <div class="card-title">Result Pattern</div>
    <div class="card-description">Type-safe success/failure handling without exceptions</div>
  </div>
  <div class="card">
    <div class="card-title">Option Type</div>
    <div class="card-description">Safe null handling with Some/None semantics</div>
  </div>
  <div class="card">
    <div class="card-title">Contracts</div>
    <div class="card-description">Common interfaces for consistent APIs</div>
  </div>
  <div class="card">
    <div class="card-title">Traits</div>
    <div class="card-description">Reusable behaviors for your classes</div>
  </div>
</div>

## Features

### Result Pattern

Handle success and failure explicitly:

```php
use Esegments\Core\Results\Result;

function divide(int $a, int $b): Result
{
    if ($b === 0) {
        return Result::failure('Division by zero');
    }

    return Result::success($a / $b);
}

$result = divide(10, 2);

if ($result->isSuccess()) {
    echo $result->value(); // 5
}
```

### Option Type

Safe null handling:

```php
use Esegments\Core\Results\Option;

function findUser(int $id): Option
{
    $user = User::find($id);

    return Option::fromNullable($user);
}

$user = findUser(123)
    ->map(fn($u) => $u->name)
    ->getOrElse('Guest');
```

### Reusable Traits

| Trait | Purpose |
|-------|---------|
| `Makeable` | Static `make()` factory method |
| `HasUuid` | UUID generation for models |
| `BootableTrait` | Automatic boot methods |
| `ConfigurableTrait` | Configuration handling |
| `ForwardsCalls` | Method forwarding |

### Enum Concerns

Add Filament-compatible methods to enums:

```php
use Esegments\Core\Enums\Concerns\HasLabel;
use Esegments\Core\Enums\Concerns\HasColor;
use Esegments\Core\Enums\Concerns\HasIcon;

enum Status: string implements HasLabel, HasColor, HasIcon
{
    use HasLabel, HasColor, HasIcon;

    case Active = 'active';
    case Inactive = 'inactive';
}
```

## Installation

```bash
composer require esegments/core
```

<div class="callout callout-info">
  <strong>Next Steps:</strong> Check out the <a href="/core/docs/getting-started/">Getting Started guide</a> for detailed usage examples.
</div>
