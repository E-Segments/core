<?php

declare(strict_types=1);

namespace Esegments\Core\Enums\Concerns;

/**
 * Trait for enums that have a human-readable label.
 *
 * Implements Filament's HasLabel contract pattern.
 */
trait HasLabel
{
    /**
     * Get the human-readable label for this enum case.
     */
    public function getLabel(): string
    {
        // Try to use a labels() method if defined
        if (method_exists($this, 'labels')) {
            $labels = $this->labels();
            if (isset($labels[$this->value])) {
                return $labels[$this->value];
            }
        }

        // Default: Convert case name to words
        return str_replace('_', ' ', ucwords(strtolower($this->name), '_'));
    }

    /**
     * Get all labels keyed by value.
     *
     * @return array<string|int, string>
     */
    public static function getLabels(): array
    {
        $labels = [];

        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->getLabel();
        }

        return $labels;
    }
}
