<?php

namespace App\Services\ResourceSystem\Columns;

class BadgeColumn extends Column
{
    /**
     * The badge colors.
     *
     * @var array
     */
    protected $colors = [];

    /**
     * Set the badge colors.
     *
     * @param  array  $colors
     * @return $this
     */
    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Get the badge colors.
     *
     * @return array
     */
    public function getColors(): array
    {
        return $this->colors;
    }

    /**
     * Get the color for a specific value.
     *
     * @param  string  $value
     * @return string|null
     */
    public function getColorForValue(string $value): ?string
    {
        return $this->colors[$value] ?? null;
    }

    /**
     * Get the component name for the column.
     *
     * @return string
     */
    public function component(): string
    {
        return 'resource-system::columns.badge-column';
    }

    /**
     * Get the column's attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'colors' => $this->getColors(),
        ]);
    }

    /**
     * Format the value for display.
     *
     * @param  mixed  $value
     * @param  mixed  $resource
     * @return mixed
     */
    public function formatValue($value, $resource)
    {
        if ($this->formatValueCallback) {
            $value = call_user_func($this->formatValueCallback, $value, $resource);
        }

        return [
            'value' => $value,
            'color' => $this->colors[$value] ?? 'secondary',
        ];
    }
}
