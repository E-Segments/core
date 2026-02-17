<?php

declare(strict_types=1);

namespace Esegments\Core\Enums\Concerns;

/**
 * Trait for enums that have a color representation.
 *
 * Implements Filament's HasColor contract pattern.
 * Colors should be Tailwind color names: primary, secondary, success, warning, danger, info, gray.
 */
trait HasColor
{
    /**
     * Get the color for this enum case.
     *
     * @return string|array{50: string, ..., 950: string}|null
     */
    public function getColor(): string|array|null
    {
        // Try to use a colors() method if defined
        if (method_exists($this, 'colors')) {
            $colors = $this->colors();
            if (isset($colors[$this->value])) {
                return $colors[$this->value];
            }
        }

        return null;
    }

    /**
     * Get all colors keyed by value.
     *
     * @return array<string|int, string|array|null>
     */
    public static function getColors(): array
    {
        $colors = [];

        foreach (self::cases() as $case) {
            $colors[$case->value] = $case->getColor();
        }

        return $colors;
    }
}
