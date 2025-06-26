<?php

namespace App\Services\ResourceSystem\Fields;

use Illuminate\Support\Str;

class BelongsToMany extends Select
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
     * The relationship name.
     *
     * @var string
     */
    protected $relationship;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->multiple();
        $this->relationship = $name;

        // Try to guess the related model from the field name
        $modelName = Str::studly(Str::singular($name));
        $this->relatedModel = "App\\Models\\{$modelName}";
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
     * Set the relationship name.
     *
     * @param  string  $relationship
     * @return $this
     */
    public function relationship(string $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Get the relationship name.
     *
     * @return string
     */
    public function getRelationshipName(): string
    {
        return $this->relationship;
    }
    
    /**
     * Get the options for the select field.
     *
     * @return array
     */
    public function getOptions(): array
    {
        if ($this->options !== null && count($this->options) > 0) {
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
} 