<?php

namespace App\Livewire\Forms\Previews;

use App\Models\FormField;
use Livewire\Component;
use Livewire\Attributes\On;

class Section extends Component
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
                <hr class="dark:border-gray-700 my-4">
                <h3 class="font-semibold text-lg">{{ $this->field->label }}</h3>
                @if($this->field->placeholder)
                    <p class="text-sm text-gray-500">{{ $this->field->placeholder }}</p>
                @endif
            </div>
        BLADE;
    }
} 