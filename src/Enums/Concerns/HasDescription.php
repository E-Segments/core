<?php

declare(strict_types=1);

namespace Esegments\Core\Enums\Concerns;

/**
 * Trait for enums that have a description.
 *
 * Useful for providing help text or tooltips.
 */
trait HasDescription
{
    /**
     * Get the description for this enum case.
     */
    public function getDescription(): ?string
    {
        // Try to use a descriptions() method if defined
        if (method_exists($this, 'descriptions')) {
            $descriptions = $this->descriptions();
            if (isset($descriptions[$this->value])) {
                return $descriptions[$this->value];
            }
        }

        return null;
    }

    /**
     * Get all descriptions keyed by value.
     *
     * @return array<string|int, string|null>
     */
    public static function getDescriptions(): array
    {
        $descriptions = [];

        foreach (self::cases() as $case) {
            $descriptions[$case->value] = $case->getDescription();
        }

        return $descriptions;
    }
}
