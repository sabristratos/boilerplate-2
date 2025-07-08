<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

/**
 * Data Transfer Object for a form element.
 *
 * @property string $id
 * @property string $type
 * @property int $order
 * @property array $properties
 * @property array $styles
 * @property array $validation
 */
class ElementDTO
{
    public string $id;

    public string $type;

    public int $order;

    public array $properties;

    public array $styles;

    public array $validation;

    /**
     * ElementDTO constructor.
     *
     * @param array $data The element data
     * @throws \InvalidArgumentException If required fields are missing
     */
    public function __construct(array $data)
    {
        if (empty($data['id'])) {
            throw new \InvalidArgumentException(__('forms.errors.element_id_required'));
        }

        if (empty($data['type'])) {
            throw new \InvalidArgumentException(__('forms.errors.element_type_required'));
        }

        $this->id = $data['id'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->order = $data['order'] ?? 0;
        $this->properties = $data['properties'] ?? [];
        $this->styles = $data['styles'] ?? [];
        $this->validation = $data['validation'] ?? [];
    }

    /**
     * Fill the DTO with new data.
     *
     * @param array $data The new data to fill
     */
    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array The DTO as an array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'order' => $this->order,
            'properties' => $this->properties,
            'styles' => $this->styles,
            'validation' => $this->validation,
        ];
    }
}
