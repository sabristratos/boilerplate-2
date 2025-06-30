<?php

namespace App\Services\ResourceSystem\Fields;

use Illuminate\Support\Str;

abstract class Field
{
    /**
     * The field's label.
     *
     * @var string
     */
    protected $label;

    /**
     * The field's help text.
     *
     * @var string|null
     */
    protected $helpText;

    /**
     * The field's placeholder.
     *
     * @var string|null
     */
    protected $placeholder;

    /**
     * The validation rules for the field.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the field is required.
     *
     * @var bool
     */
    protected $required = false;

    /**
     * Whether the field is readonly.
     *
     * @var bool
     */
    protected $readonly = false;

    /**
     * Whether the field is disabled.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * Whether the field should trigger a refresh on change.
     *
     * @var bool
     */
    protected $reactive = false;

    /**
     * The field's default value.
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Create a new field.
     *
     * @return void
     */
    public function __construct(protected string $name)
    {
        $this->label = Str::title(Str::replace('_', ' ', $this->name));
    }

    /**
     * Create a new field instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set the field's label.
     *
     * @return $this
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the field's help text.
     *
     * @return $this
     */
    public function helpText(string $helpText): static
    {
        $this->helpText = $helpText;

        return $this;
    }

    /**
     * Set the field's placeholder.
     *
     * @return $this
     */
    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Set the validation rules for the field.
     *
     * @param  array|string  $rules
     * @return $this
     */
    public function rules($rules): static
    {
        $this->rules = is_string($rules) ? explode('|', $rules) : $rules;

        if (in_array('required', $this->rules)) {
            $this->required = true;
        }

        return $this;
    }

    /**
     * Mark the field as required.
     *
     * @return $this
     */
    public function required(bool $value = true): static
    {
        $this->required = $value;

        if ($value && ! in_array('required', $this->rules)) {
            $this->rules[] = 'required';
        } elseif (! $value && in_array('required', $this->rules)) {
            $this->rules = array_filter($this->rules, fn ($rule): bool => $rule !== 'required');
        }

        return $this;
    }

    /**
     * Mark the field as readonly.
     *
     * @return $this
     */
    public function readonly(bool $value = true): static
    {
        $this->readonly = $value;

        return $this;
    }

    /**
     * Mark the field as disabled.
     *
     * @return $this
     */
    public function disabled(bool $value = true): static
    {
        $this->disabled = $value;

        return $this;
    }

    /**
     * Mark the field as reactive.
     *
     * @return $this
     */
    public function reactive(bool $value = true): static
    {
        $this->reactive = $value;

        return $this;
    }

    /**
     * Set the field's default value.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function default($value): static
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * Get the field's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the field's label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the field's help text.
     */
    public function getHelpText(): ?string
    {
        return $this->helpText;
    }

    /**
     * Get the field's placeholder.
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * Get the validation rules for the field.
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Determine if the field is required.
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Determine if the field is readonly.
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * Determine if the field is disabled.
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Check if the field is reactive.
     */
    public function isReactive(): bool
    {
        return $this->reactive;
    }

    /**
     * Get the field's default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Get the component name for the field.
     */
    abstract public function component(): string;

    /**
     * Get the field's attributes.
     */
    public function getAttributes(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'help_text' => $this->getHelpText(),
            'placeholder' => $this->getPlaceholder(),
            'required' => $this->isRequired(),
            'readonly' => $this->isReadonly(),
            'disabled' => $this->isDisabled(),
            'default' => $this->getDefaultValue(),
        ];
    }
}
