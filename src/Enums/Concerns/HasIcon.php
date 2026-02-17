<?php

declare(strict_types=1);

namespace Esegments\Core\Enums\Concerns;

/**
 * Trait for enums that have an icon representation.
 *
 * Implements Filament's HasIcon contract pattern.
 * Icons should be Blade component names, e.g. 'heroicon-o-check'.
 */
trait HasIcon
{
    /**
     * Get the icon for this enum case.
     */
    public function getIcon(): ?string
    {
        // Try to use an icons() method if defined
        if (method_exists($this, 'icons')) {
            $icons = $this->icons();
            if (isset($icons[$this->value])) {
                return $icons[$this->value];
            }
        }

        return null;
    }

    /**
     * Get all icons keyed by value.
     *
     * @return array<string|int, string|null>
     */
    public static function getIcons(): array
    {
        $icons = [];

        foreach (self::cases() as $case) {
            $icons[$case->value] = $case->getIcon();
        }

        return $icons;
    }
}
