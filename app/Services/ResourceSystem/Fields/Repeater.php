<?php

namespace App\Services\ResourceSystem\Fields;

class Repeater extends Field
{
    /**
     * The fields for the repeater.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Set the fields for the repeater.
     *
     * @return $this
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the fields for the repeater.
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * The component to be used for the field.
     *
     * @var string
     */
    public function component(): string
    {
        return 'repeater';
    }
}
