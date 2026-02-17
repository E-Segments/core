<?php

declare(strict_types=1);

namespace Esegments\Core\Concerns;

use Illuminate\Support\Str;

/**
 * Trait for classes that use UUID identifiers.
 */
trait HasUuid
{
    /**
     * The UUID attribute name.
     */
    protected static string $uuidColumn = 'uuid';

    /**
     * Boot the trait.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            $column = static::$uuidColumn;
            if (empty($model->{$column})) {
                $model->{$column} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the UUID column name.
     */
    public function getUuidColumn(): string
    {
        return static::$uuidColumn;
    }

    /**
     * Get the UUID value.
     */
    public function getUuid(): ?string
    {
        return $this->{static::$uuidColumn};
    }

    /**
     * Find a model by UUID.
     *
     * @return static|null
     */
    public static function findByUuid(string $uuid): ?static
    {
        return static::where(static::$uuidColumn, $uuid)->first();
    }

    /**
     * Find a model by UUID or fail.
     *
     * @return static
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByUuidOrFail(string $uuid): static
    {
        return static::where(static::$uuidColumn, $uuid)->firstOrFail();
    }

    /**
     * Get the route key name (use UUID for routing).
     */
    public function getRouteKeyName(): string
    {
        return static::$uuidColumn;
    }
}
