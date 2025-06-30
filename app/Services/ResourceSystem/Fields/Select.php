<?php

namespace App\Services\ResourceSystem\Fields;

class Select extends Field
{
    /**
     * The field's options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Whether the select allows multiple selections.
     *
     * @var bool
     */
    protected $multiple = false;

    /**
     * Set the field's options.
     *
     * @return $this
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the field's options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set whether the select allows multiple selections.
     *
     * @return $this
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Check if the select allows multiple selections.
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * The component to be used for the field.
     *
     * @var string
     */
    public function component(): string
    {
        return 'select';
    }
}
