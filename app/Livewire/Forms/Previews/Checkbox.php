<?php

namespace App\Livewire\Forms\Previews;

use App\Models\FormField;
use Livewire\Component;
use Livewire\Attributes\On;

class Checkbox extends Component
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
            <div>
                @if(is_array($this->field->options) && !empty($this->field->options))
                    <label class="flux-label">{{ $this->field->label }}</label>
                    <div class="space-y-2 mt-2">
                        @foreach($this->field->options as $option)
                            <flux:checkbox label="{{ $option['label'] }}" value="{{ $option['value'] }}" />
                        @endforeach
                    </div>
                @else
                    <flux:checkbox :label="$this->field->label" :required="$this->field->is_required" />
                @endif
            </div>
        BLADE;
    }
} 