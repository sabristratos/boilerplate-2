<?php

namespace App\Services\ResourceSystem\Columns;

use Illuminate\Support\Str;

class Column
{
    /**
     * The column's label.
     *
     * @var string
     */
    protected $label;

    /**
     * Whether the column is sortable.
     *
     * @var bool
     */
    protected $sortable = false;

    /**
     * Whether the column is searchable.
     *
     * @var bool
     */
    protected $searchable = false;

    /**
     * The column's alignment.
     *
     * @var string
     */
    protected $alignment = 'left';

    /**
     * The callback to format the value.
     *
     * @var \Closure|null
     */
    protected $formatValueCallback;

    /**
     * Create a new column.
     *
     * @return void
     */
    public function __construct(protected string $name)
    {
        $this->label = Str::title(Str::replace('_', ' ', $this->name));
    }

    /**
     * Create a new column instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set the column's label.
     *
     * @return $this
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Make the column sortable.
     *
     * @return $this
     */
    public function sortable(bool $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Make the column searchable.
     *
     * @return $this
     */
    public function searchable(bool $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Set the column's alignment.
     *
     * @return $this
     */
    public function alignment(string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    /**
     * Set the callback to format the value.
     *
     * @return $this
     */
    public function setFormatValueCallback(\Closure $callback): static
    {
        $this->formatValueCallback = $callback;

        return $this;
    }

    /**
     * Align the column to the left.
     *
     * @return $this
     */
    public function alignLeft(): static
    {
        return $this->alignment('left');
    }

    /**
     * Align the column to the center.
     *
     * @return $this
     */
    public function alignCenter(): static
    {
        return $this->alignment('center');
    }

    /**
     * Align the column to the right.
     *
     * @return $this
     */
    public function alignRight(): static
    {
        return $this->alignment('right');
    }

    /**
     * Get the column's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the column's label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Determine if the column is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Determine if the column is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Get the column's alignment.
     */
    public function getAlignment(): string
    {
        return $this->alignment;
    }

    /**
     * Get the component name for the column.
     */
    public function component(): string
    {
        return 'resource-system::columns.column';
    }

    /**
     * Get the column's attributes.
     */
    public function getAttributes(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'sortable' => $this->isSortable(),
            'searchable' => $this->isSearchable(),
            'alignment' => $this->getAlignment(),
        ];
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
            return call_user_func($this->formatValueCallback, $value, $resource);
        }

        return $value;
    }

    /**
     * Apply the sort to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applySort($query, $direction)
    {
        return $query->orderBy($this->getName(), $direction);
    }
}
