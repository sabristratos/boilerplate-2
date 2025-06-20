<?php

namespace App\Services\ResourceSystem\Fields;

class Text extends Field
{
    /**
     * The field's type.
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * Set the field's type.
     *
     * @param  string  $type
     * @return $this
     */
    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the field's type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the component name for the field.
     *
     * @return string
     */
    public function component(): string
    {
        return 'resource-system::fields.text';
    }

    /**
     * Get the field's attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'type' => $this->getType(),
        ]);
    }
}
