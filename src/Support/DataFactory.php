<?php

declare(strict_types=1);

namespace Esegments\Core\Support;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

/**
 * Factory for creating Data objects from various sources.
 *
 * Provides convenient methods for creating DTOs from requests,
 * arrays, models, and other data sources.
 */
final class DataFactory
{
    /**
     * Create a Data object from a request.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dataClass
     * @return T
     */
    public static function fromRequest(string $dataClass, Request $request): Data
    {
        return $dataClass::from($request);
    }

    /**
     * Create a Data object from an array.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dataClass
     * @param  array<string, mixed>  $data
     * @return T
     */
    public static function fromArray(string $dataClass, array $data): Data
    {
        return $dataClass::from($data);
    }

    /**
     * Create a Data object from a model.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dataClass
     * @param  object  $model
     * @return T
     */
    public static function fromModel(string $dataClass, object $model): Data
    {
        return $dataClass::from($model);
    }

    /**
     * Create a collection of Data objects from an array.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dataClass
     * @param  iterable<array<string, mixed>>  $items
     * @return \Spatie\LaravelData\DataCollection<int, T>
     */
    public static function collection(string $dataClass, iterable $items): \Spatie\LaravelData\DataCollection
    {
        return $dataClass::collect($items);
    }

    /**
     * Safely create a Data object, returning null on failure.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dataClass
     * @param  mixed  $data
     * @return T|null
     */
    public static function tryFrom(string $dataClass, mixed $data): ?Data
    {
        try {
            return $dataClass::from($data);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Create a Data object or return a default.
     *
     * @template T of Data
     *
     * @param  class-string<T>  $dataClass
     * @param  mixed  $data
     * @param  T  $default
     * @return T
     */
    public static function fromOrDefault(string $dataClass, mixed $data, Data $default): Data
    {
        return self::tryFrom($dataClass, $data) ?? $default;
    }
}
