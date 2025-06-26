<?php

namespace App\Livewire\Forms\Previews;

use App\Models\FormField;
use Livewire\Component;
use Livewire\Attributes\On;

class Radio extends Component
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
            <flux:radio.group :label="$this->field->label" :required="$this->field->is_required">
                 @if(is_array($this->field->options))
                    @foreach($this->field->options as $option)
                        <flux:radio label="{{ $option['label'] }}" value="{{ $option['value'] }}" />
                    @endforeach
                @endif
            </flux:radio.group>
        BLADE;
    }
} 