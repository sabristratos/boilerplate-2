<?php

namespace App\Services\ResourceSystem\Filters;

use Illuminate\Support\Str;

abstract class Filter
{
    /**
     * The filter's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The filter's label.
     *
     * @var string
     */
    protected $label;

    /**
     * Create a new filter.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = Str::title(Str::replace('_', ' ', $name));
    }

    /**
     * Create a new filter instance.
     *
     * @param  string  $name
     * @return static
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set the filter's label.
     *
     * @param  string  $label
     * @return $this
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the filter's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the filter's label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the component name for the filter.
     *
     * @return string
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
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
        ];
    }
}
