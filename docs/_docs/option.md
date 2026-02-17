---
title: "Option Type"
description: "Safe null handling with Some/None semantics"
order: 3
---

The Option type represents a value that may or may not exist, eliminating null pointer exceptions.

## Why Use Option?

```php
// Without Option - null checks everywhere
function getOrderTotal(Order $order): ?float
{
    $customer = $order->customer;
    if ($customer === null) {
        return null;
    }

    $discount = $customer->discount;
    if ($discount === null) {
        return null;
    }

    return $order->subtotal * (1 - $discount);
}

// With Option - chainable null handling
function getOrderTotal(Order $order): Option
{
    return Option::fromNullable($order->customer)
        ->flatMap(fn($c) => Option::fromNullable($c->discount))
        ->map(fn($d) => $order->subtotal * (1 - $d));
}
```

## Creating Options

### From Nullable

```php
use Esegments\Core\Results\Option;

// Value exists
$option = Option::fromNullable($user);  // Some($user)

// Value is null
$option = Option::fromNullable(null);   // None
```

### Explicit Construction

```php
$some = Option::some($value);  // Always Some
$none = Option::none();        // Always None
```

### From Condition

```php
// Some if condition is true
$option = Option::when($user->isActive(), $user);

// Some if callback returns true
$option = Option::filter($value, fn($v) => $v > 0);
```

## Checking State

```php
$option->isSome();  // true if has value
$option->isNone();  // true if empty
```

## Getting Values

### Unsafe Get

```php
$some = Option::some(42);
$some->get();  // 42

$none = Option::none();
$none->get();  // throws OptionException
```

### Safe Alternatives

```php
// Default value
$value = $option->getOrElse(0);

// Computed default
$value = $option->getOrCall(fn() => computeDefault());

// Throw custom exception
$value = $option->getOrThrow(new NotFoundException());

// Convert to nullable
$value = $option->toNullable();  // value or null
```

## Transforming Options

### map()

Transform the value if present:

```php
Option::some('hello')
    ->map(fn($s) => strtoupper($s));  // Some('HELLO')

Option::none()
    ->map(fn($s) => strtoupper($s));  // None
```

### flatMap()

Chain operations returning Options:

```php
function findUser(int $id): Option { /* ... */ }
function getEmail(User $user): Option { /* ... */ }

$email = findUser(123)
    ->flatMap(fn($user) => getEmail($user))
    ->getOrElse('no-email@example.com');
```

### filter()

Keep value only if predicate passes:

```php
Option::some(10)
    ->filter(fn($x) => $x > 5);   // Some(10)

Option::some(3)
    ->filter(fn($x) => $x > 5);   // None
```

## Conditional Execution

### ifSome()

Execute callback if value exists:

```php
$option->ifSome(function ($value) {
    Log::info("Found value: $value");
});
```

### ifNone()

Execute callback if empty:

```php
$option->ifNone(function () {
    Log::warning("Value not found");
});
```

### match()

Handle both cases:

```php
$message = $option->match(
    onSome: fn($value) => "Found: $value",
    onNone: fn() => "Not found"
);
```

## Combining Options

### orElse()

Fallback to another Option:

```php
$primary = findInCache($key);
$fallback = findInDatabase($key);

$value = $primary->orElse($fallback);
```

### and()

Require both to be present:

```php
$firstName = Option::some('John');
$lastName = Option::some('Doe');

$fullName = $firstName->and($lastName)
    ->map(fn($f, $l) => "$f $l");  // Some('John Doe')
```

## Converting to Result

```php
$option = Option::some(42);

// Convert to Result
$result = $option->toResult('Value was missing');
// Success(42)

$none = Option::none();
$result = $none->toResult('Value was missing');
// Failure('Value was missing')
```

## Real-World Example

```php
class UserRepository
{
    public function findByEmail(string $email): Option
    {
        $user = User::where('email', $email)->first();
        return Option::fromNullable($user);
    }
}

class AuthService
{
    public function authenticate(string $email, string $password): Result
    {
        return $this->userRepo->findByEmail($email)
            ->filter(fn($user) => Hash::check($password, $user->password))
            ->toResult('Invalid credentials')
            ->map(fn($user) => $this->createSession($user));
    }
}

// Usage
$result = $authService->authenticate($email, $password);

return $result->match(
    onSuccess: fn($session) => redirect()->intended('/dashboard'),
    onFailure: fn($error) => back()->withErrors(['email' => $error])
);
```

## Collection Integration

```php
$users = collect([
    Option::some(User::find(1)),
    Option::none(),
    Option::some(User::find(3)),
]);

// Get all present values
$presentUsers = $users
    ->filter(fn($opt) => $opt->isSome())
    ->map(fn($opt) => $opt->get());

// Using Option::flatten helper
$presentUsers = Option::flatten($users);
```
