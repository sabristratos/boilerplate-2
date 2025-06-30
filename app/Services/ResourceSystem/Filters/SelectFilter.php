<?php

namespace App\Services\ResourceSystem\Filters;

class SelectFilter extends Filter
{
    /**
     * The options for the select filter.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The custom apply callback.
     *
     * @var callable|null
     */
    protected $applyCallback;

    /**
     * Whether to include an empty option.
     *
     * @var bool
     */
    protected $includeEmptyOption = true;

    /**
     * The label for the empty option.
     *
     * @var string
     */
    protected $emptyOptionLabel = 'resources.filters.all';

    /**
     * Set the options for the select filter.
     *
     * @return $this
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set whether to include an empty option.
     *
     * @return $this
     */
    public function includeEmptyOption(bool $value = true): static
    {
        $this->includeEmptyOption = $value;

        return $this;
    }

    /**
     * Set the label for the empty option.
     *
     * @return $this
     */
    public function emptyOptionLabel(string $label): static
    {
        $this->emptyOptionLabel = $label;

        return $this;
    }

    /**
     * Get the options for the select filter.
     */
    public function getOptions(): array
    {
        $options = $this->options;

        if ($this->includeEmptyOption) {
            $options = ['' => $this->emptyOptionLabel] + $options;
        }

        return $options;
    }

    /**
     * Get the component name for the filter.
     */
    public function component(): string
    {
        return 'resource-system::filters.select-filter';
    }

    /**
     * Set a custom callback to apply the filter.
     *
     * @return $this
     */
    public function setApplyCallback(callable $callback): static
    {
        $this->applyCallback = $callback;

        return $this;
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($query, $value)
    {
        if ($this->applyCallback) {
            return call_user_func($this->applyCallback, $query, $value);
        }

        if ($value === null || $value === '') {
            return $query;
        }

        return $query->where($this->getName(), $value);
    }

    /**
     * Get the filter's attributes.
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'options' => $this->getOptions(),
        ]);
    }
}
