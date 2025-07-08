<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

/**
 * Service for managing icons used in form elements.
 */
class IconService
{
    private array $availableIcons;

    private readonly array $categories;

    /**
     * IconService constructor.
     */
    public function __construct()
    {
        $this->availableIcons = config('forms.icons.available');
        $this->categories = config('forms.icons.categories');
    }

    /**
     * Get all available icons for form elements.
     */
    public function getAvailableIcons(): array
    {
        return $this->availableIcons;
    }

    /**
     * Check if an icon exists.
     */
    public function iconExists(string $iconName): bool
    {
        return array_key_exists($iconName, $this->availableIcons);
    }

    /**
     * Get icon label by name.
     */
    public function getIconLabel(string $iconName): ?string
    {
        return $this->availableIcons[$iconName] ?? null;
    }

    /**
     * Get icons grouped by category.
     */
    public function getIconsByCategory(): array
    {
        $groupedIcons = [];

        foreach ($this->categories as $category => $iconKeys) {
            $groupedIcons[$category] = [];
            foreach ($iconKeys as $iconKey) {
                if (isset($this->availableIcons[$iconKey])) {
                    $groupedIcons[$category][$iconKey] = $this->availableIcons[$iconKey];
                }
            }
        }

        return $groupedIcons;
    }
}
