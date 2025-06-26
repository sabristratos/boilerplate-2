<?php

namespace App\Livewire\Forms\Previews;

use App\Models\FormField;
use Livewire\Component;
use Livewire\Attributes\On;

class Text extends Component
{
    public FormField $field;

    public function mount(int $fieldId)
    {
        $this->field = FormField::find($fieldId);
    }

    #[On('field-updated')]
    public function refreshField($fieldId)
    {
        if ($this->field->id === $fieldId) {
            $this->field->refresh();
        }
    }

    public function render()
    {
        return <<<'BLADE'
            <flux:input :label="$this->field->label" :placeholder="$this->field->placeholder" :type="$this->field->type->value" :required="$this->field->is_required" />
        BLADE;
    }
} 