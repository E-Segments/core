---
title: "Getting Started"
description: "Learn how to install and use Esegments Core"
order: 1
---

## Requirements

- PHP 8.2+
- Laravel 11+ (optional, for service provider)

## Installation

Install via Composer:

```bash
composer require esegments/core
```

The service provider is auto-discovered in Laravel applications.

## Result Pattern

The Result pattern provides explicit success/failure handling without exceptions.

### Creating Results

```php
use Esegments\Core\Results\Result;
use Esegments\Core\Results\Success;
use Esegments\Core\Results\Failure;

// Static factory methods
$success = Result::success($value);
$failure = Result::failure('Error message');

// Direct instantiation
$success = new Success($value);
$failure = new Failure('Error message');
```

### Checking Results

```php
$result = someOperation();

if ($result->isSuccess()) {
    $value = $result->value();
}

if ($result->isFailure()) {
    $error = $result->error();
}
```

### Transforming Results

```php
$result = Result::success(5)
    ->map(fn($x) => $x * 2)        // Success(10)
    ->flatMap(fn($x) => divide($x, 2)); // Chain operations
```

### Handling Both Cases

```php
$output = $result->match(
    onSuccess: fn($value) => "Got: $value",
    onFailure: fn($error) => "Error: $error"
);
```

### Getting Values Safely

```php
// Get value or default
$value = $result->getOrElse(0);

// Get value or compute default
$value = $result->getOrCall(fn() => computeDefault());

// Get value or throw
$value = $result->getOrThrow();
```

## Option Type

The Option type safely handles nullable values.

### Creating Options

```php
use Esegments\Core\Results\Option;

// From nullable value
$option = Option::fromNullable($user);

// Explicit Some/None
$some = Option::some($value);
$none = Option::none();
```

### Checking Options

```php
if ($option->isSome()) {
    $value = $option->get();
}

if ($option->isNone()) {
    // Handle missing value
}
```

### Transforming Options

```php
$name = Option::fromNullable($user)
    ->map(fn($u) => $u->name)
    ->filter(fn($name) => strlen($name) > 0)
    ->getOrElse('Anonymous');
```

### Flattening Nested Options

```php
$result = Option::fromNullable($order)
    ->flatMap(fn($o) => Option::fromNullable($o->customer))
    ->flatMap(fn($c) => Option::fromNullable($c->email));
```

## Next Steps

- [Result Pattern](/docs/result/) - In-depth guide
- [Option Type](/docs/option/) - Complete reference
- [Traits](/docs/traits/) - Reusable behaviors
