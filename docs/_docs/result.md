---
title: "Result Pattern"
description: "Type-safe success/failure handling"
order: 2
---

The Result pattern represents the outcome of an operation that might fail, without using exceptions for control flow.

## Why Use Result?

```php
// Without Result - exceptions for control flow
function processOrder(Order $order): Order
{
    if (!$order->isValid()) {
        throw new InvalidOrderException('Order is invalid');
    }
    // Process...
    return $order;
}

// With Result - explicit failure handling
function processOrder(Order $order): Result
{
    if (!$order->isValid()) {
        return Result::failure('Order is invalid');
    }
    // Process...
    return Result::success($order);
}
```

## Creating Results

### Success

```php
use Esegments\Core\Results\Result;
use Esegments\Core\Results\Success;

// Factory method
$result = Result::success($value);

// Direct instantiation
$result = new Success($value);

// With null value (still a success)
$result = Result::success(null);
```

### Failure

```php
use Esegments\Core\Results\Failure;

// Factory method
$result = Result::failure('Something went wrong');

// Direct instantiation
$result = new Failure('Error message');

// With exception
$result = Result::failure(new \RuntimeException('Failed'));
```

## Checking State

```php
$result->isSuccess();  // bool
$result->isFailure();  // bool
```

## Getting Values

### From Success

```php
$success = Result::success(42);

$success->value();        // 42
$success->getOrElse(0);   // 42
$success->getOrThrow();   // 42
```

### From Failure

```php
$failure = Result::failure('error');

$failure->error();        // 'error'
$failure->getOrElse(0);   // 0
$failure->getOrThrow();   // throws ResultException
```

## Transforming Results

### map()

Transform the success value:

```php
Result::success(5)
    ->map(fn($x) => $x * 2);  // Success(10)

Result::failure('error')
    ->map(fn($x) => $x * 2);  // Failure('error') - unchanged
```

### flatMap()

Chain operations that return Results:

```php
function divide(int $a, int $b): Result
{
    return $b === 0
        ? Result::failure('Division by zero')
        : Result::success($a / $b);
}

Result::success(10)
    ->flatMap(fn($x) => divide($x, 2))   // Success(5)
    ->flatMap(fn($x) => divide($x, 0));  // Failure('Division by zero')
```

### mapFailure()

Transform the error:

```php
Result::failure('not found')
    ->mapFailure(fn($e) => "Error: $e");  // Failure('Error: not found')
```

## Pattern Matching

Handle both cases at once:

```php
$message = $result->match(
    onSuccess: fn($value) => "Order #{$value->id} processed",
    onFailure: fn($error) => "Failed: {$error}"
);
```

## Try/Catch Conversion

Convert exceptions to Results:

```php
$result = Result::try(function () {
    return riskyOperation();
});

// $result is Success if no exception
// $result is Failure with the exception if thrown
```

## Filtering

```php
Result::success(10)
    ->filter(fn($x) => $x > 5, 'Too small');  // Success(10)

Result::success(3)
    ->filter(fn($x) => $x > 5, 'Too small');  // Failure('Too small')
```

## Combining Results

### all()

All must succeed:

```php
$results = Result::all([
    Result::success(1),
    Result::success(2),
    Result::success(3),
]);
// Success([1, 2, 3])

$results = Result::all([
    Result::success(1),
    Result::failure('error'),
    Result::success(3),
]);
// Failure('error')
```

### any()

First success wins:

```php
$result = Result::any([
    Result::failure('a'),
    Result::success(2),
    Result::failure('c'),
]);
// Success(2)
```

## Real-World Example

```php
class OrderService
{
    public function createOrder(CreateOrderData $data): Result
    {
        return $this->validateStock($data)
            ->flatMap(fn($d) => $this->calculateTotals($d))
            ->flatMap(fn($d) => $this->processPayment($d))
            ->flatMap(fn($d) => $this->saveOrder($d))
            ->map(fn($order) => $this->sendConfirmation($order));
    }

    private function validateStock(CreateOrderData $data): Result
    {
        foreach ($data->items as $item) {
            if (!$this->inventory->hasStock($item->productId, $item->quantity)) {
                return Result::failure("Insufficient stock for product {$item->productId}");
            }
        }
        return Result::success($data);
    }

    // ... other methods return Result
}

// Usage
$result = $orderService->createOrder($data);

return $result->match(
    onSuccess: fn($order) => response()->json($order),
    onFailure: fn($error) => response()->json(['error' => $error], 400)
);
```
