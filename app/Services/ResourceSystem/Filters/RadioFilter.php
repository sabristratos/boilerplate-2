<?php

namespace App\Services\ResourceSystem\Filters;

class RadioFilter extends Filter
{
    /**
     * The filter's options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The callback to apply the filter.
     *
     * @var \Closure|null
     */
    protected $applyCallback;

    /**
     * Set the filter's options.
     *
     * @return $this
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the filter's options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set the callback to apply the filter.
     *
     * @return $this
     */
    public function setApplyCallback(\Closure $callback): static
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
            return ($this->applyCallback)($query, $value);
        }

        if ($value) {
            return $query->where($this->getName(), $value);
        }

        return $query;
    }

    /**
     * Get the component name for the filter.
     */
    public function component(): string
    {
        return 'radio-filter';
    }
}
