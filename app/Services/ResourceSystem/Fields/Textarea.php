<?php

namespace App\Services\ResourceSystem\Fields;

class Textarea extends Field
{
    /**
     * The number of rows for the textarea.
     *
     * @var int
     */
    protected $rows = 3;

    /**
     * Set the number of rows for the textarea.
     *
     * @return $this
     */
    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Get the number of rows for the textarea.
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * Get the component name for the field.
     */
    public function component(): string
    {
        return 'resource-system::fields.textarea';
    }

    /**
     * Get the field's attributes.
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'rows' => $this->getRows(),
        ]);
    }
}
