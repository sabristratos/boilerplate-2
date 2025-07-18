<?php

namespace App\Services\ResourceSystem\Fields;

class Editor extends Field
{
    /**
     * The field's toolbar.
     *
     * @var string|null
     */
    protected $toolbar;

    /**
     * Set the field's toolbar.
     *
     * @return $this
     */
    public function toolbar(string $toolbar): static
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    /**
     * Get the field's toolbar.
     */
    public function getToolbar(): ?string
    {
        return $this->toolbar;
    }

    /**
     * Get the component name for the field.
     */
    public function component(): string
    {
        return 'resource-system::fields.editor';
    }

    /**
     * Get the field's attributes.
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'toolbar' => $this->getToolbar(),
        ]);
    }
}
