<?php

namespace App\Services\ResourceSystem\Filters;

use Illuminate\Support\Str;

abstract class Filter
{
    /**
     * The filter's label.
     *
     * @var string
     */
    protected $label;

    /**
     * Create a new filter.
     *
     * @return void
     */
    public function __construct(protected string $name)
    {
        $this->label = Str::title(Str::replace('_', ' ', $this->name));
    }

    /**
     * Create a new filter instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set the filter's label.
     *
     * @return $this
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the filter's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the filter's label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the component name for the filter.
     */
    abstract public function component(): string;

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply($query, $value);

    /**
     * Get the filter's attributes.
     */
    public function getAttributes(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
        ];
    }
}
