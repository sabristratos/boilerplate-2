<?php

namespace App\Services\FormBuilder;

/**
 * Service for managing icons used in form elements.
 */
class IconService
{
    /** @var array */
    private array $availableIcons;

    /** @var array */
    private array $categories;

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
     *
     * @return array
     */
    public function getAvailableIcons(): array
    {
        return $this->availableIcons;
    }

    /**
     * Check if an icon exists.
     *
     * @param string $iconName
     * @return bool
     */
    public function iconExists(string $iconName): bool
    {
        return array_key_exists($iconName, $this->availableIcons);
    }

    /**
     * Get icon label by name.
     *
     * @param string $iconName
     * @return string|null
     */
    public function getIconLabel(string $iconName): ?string
    {
        return $this->availableIcons[$iconName] ?? null;
    }

    /**
     * Get icons grouped by category.
     *
     * @return array
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
