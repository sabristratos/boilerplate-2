<?php

namespace App\Services\ResourceSystem\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BelongsTo extends Field
{
    /**
     * The related model class.
     *
     * @var string|null
     */
    protected $relatedModel;

    /**
     * The display attribute for the related model.
     *
     * @var string
     */
    protected $displayAttribute = 'name';

    /**
     * The foreign key for the relationship.
     *
     * @var string|null
     */
    protected $foreignKey;

    /**
     * The options for the select field.
     *
     * @var array|null
     */
    protected $options;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        // Try to guess the related model from the field name
        $modelName = Str::studly(Str::singular($name));
        $this->relatedModel = "App\\Models\\{$modelName}";

        // Try to guess the foreign key
        $this->foreignKey = "{$name}_id";
    }

    /**
     * Set the related model class.
     *
     * @param  string  $relatedModel
     * @return $this
     */
    public function relatedModel(string $relatedModel): static
    {
        $this->relatedModel = $relatedModel;

        return $this;
    }

    /**
     * Set the display attribute for the related model.
     *
     * @param  string  $displayAttribute
     * @return $this
     */
    public function displayAttribute(string $displayAttribute): static
    {
        $this->displayAttribute = $displayAttribute;

        return $this;
    }

    /**
     * Set the foreign key for the relationship.
     *
     * @param  string  $foreignKey
     * @return $this
     */
    public function foreignKey(string $foreignKey): static
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    /**
     * Set the options for the select field.
     *
     * @param  array  $options
     * @return $this
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the related model class.
     *
     * @return string|null
     */
    public function getRelatedModel(): ?string
    {
        return $this->relatedModel;
    }

    /**
     * Get the display attribute for the related model.
     *
     * @return string
     */
    public function getDisplayAttribute(): string
    {
        return $this->displayAttribute;
    }

    /**
     * Get the foreign key for the relationship.
     *
     * @return string|null
     */
    public function getForeignKey(): ?string
    {
        return $this->foreignKey;
    }

    /**
     * Get the options for the select field.
     *
     * @return array
     */
    public function getOptions(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        if ($this->relatedModel === null) {
            return [];
        }

        $modelClass = $this->relatedModel;

        if (!class_exists($modelClass)) {
            return [];
        }

        return $modelClass::all()->mapWithKeys(function ($model) {
            return [$model->getKey() => $model->{$this->displayAttribute}];
        })->toArray();
    }

    /**
     * Get the component name for the field.
     *
     * @return string
     */
    public function component(): string
    {
        return 'resource-system::fields.belongs-to';
    }

    /**
     * Get the field's attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'related_model' => $this->getRelatedModel(),
            'display_attribute' => $this->getDisplayAttribute(),
            'foreign_key' => $this->getForeignKey(),
            'options' => $this->getOptions(),
        ]);
    }
}
