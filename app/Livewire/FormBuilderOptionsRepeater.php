<?php

namespace App\Livewire;

use Livewire\Component;

class FormBuilderOptionsRepeater extends Component
{
    public array $options = [];

    public string $elementIndex;

    public string $propertyPath;

    public function mount(string $elementIndex, string $propertyPath = 'options', array $options = []): void
    {
        $this->elementIndex = $elementIndex;
        $this->propertyPath = $propertyPath;

        // Ensure options is always an array
        $this->options = is_array($options) ? $options : [
            [
                'value' => '',
                'label' => '',
            ],
        ];

        // If options is empty, add at least one empty option
        if ($this->options === []) {
            $this->options = [
                [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }
    }

    public function addOption(): void
    {
        $this->options[] = [
            'value' => '',
            'label' => '',
        ];
        $this->updateParent();
    }

    public function removeOption($index): void
    {
        if (isset($this->options[$index])) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
            $this->updateParent();
        }
    }

    public function hydrate(): void
    {
        // Ensure options is always an array when component is hydrated
        if (! is_array($this->options)) {
            $this->options = [
                [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }

        // If options is empty, add at least one empty option
        if ($this->options === []) {
            $this->options = [
                [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }
    }

    public function updatedOptions($value, $key): void
    {
        // Debounce the update to avoid excessive server requests
        $this->dispatch('debounced-options-update', [
            'elementIndex' => $this->elementIndex,
            'propertyPath' => $this->propertyPath,
            'key' => $key,
            'value' => $value,
        ]);

        // Update parent immediately for real-time feedback
        $this->updateParent();
    }

    protected function updateParent(): void
    {
        // Convert options array to string format for backward compatibility
        $optionsString = '';
        foreach ($this->options as $option) {
            if (! empty($option['value']) || ! empty($option['label'])) {
                $value = $option['value'] ?: $option['label'];
                $label = $option['label'] ?: $option['value'];
                if ($value === $label) {
                    $optionsString .= $value."\n";
                } else {
                    $optionsString .= $value.'|'.$label."\n";
                }
            }
        }

        // Update the parent component with more detailed information
        $this->dispatch('options-updated', [
            'elementIndex' => $this->elementIndex,
            'propertyPath' => $this->propertyPath,
            'options' => $this->options,
            'optionsString' => trim($optionsString),
            'timestamp' => now()->timestamp,
        ]);
    }

    public function render()
    {
        return view('livewire.form-builder-options-repeater');
    }
}
