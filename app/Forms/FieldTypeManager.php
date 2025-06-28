<?php

namespace App\Forms;

use App\Forms\FieldTypes\FieldType;
use Illuminate\Support\Collection;

class FieldTypeManager
{
    protected array $fieldTypes = [];

    public function register(string $class): void
    {
        if (!is_subclass_of($class, FieldType::class)) {
            throw new \InvalidArgumentException(__('forms.field_type_manager.invalid_field_type_class', ['class' => $class, 'fieldTypeClass' => FieldType::class]));
        }

        $instance = app($class);
        $this->fieldTypes[$instance->getName()] = $instance;
    }

    public function find(string $name): ?FieldType
    {
        return $this->fieldTypes[$name] ?? null;
    }

    public function all(): Collection
    {
        return collect($this->fieldTypes);
    }
} 