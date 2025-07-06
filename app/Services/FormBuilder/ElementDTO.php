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
    /** @var string */
    public $id;

    /** @var string */
    public $type;

    /** @var int */
    public $order;

    /** @var array */
    public $properties;

    /** @var array */
    public $styles;

    /** @var array */
    public $validation;

    /**
     * ElementDTO constructor.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->order = $data['order'] ?? 0;
        $this->properties = $data['properties'] ?? [];
        $this->styles = $data['styles'] ?? [];
        $this->validation = $data['validation'] ?? [];
    }

    /**
     * Fill the DTO with new data.
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
