<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class Repeater extends Component
{
    #[Modelable]
    public array $items = [];

    public array $subfields = [];

    public string $model;

    public string $locale;

    public function mount(array $items = [], array $subfields = [], string $model = '', string $locale = 'en'): void
    {
        $this->items = $items;
        $this->subfields = $subfields;
        $this->model = $model;
        $this->locale = $locale;
    }

    public function addItem(): void
    {
        $newItem = [];
        foreach ($this->subfields as $key => $field) {
            $newItem[$key] = $field['default'] ?? '';
        }
        $this->items[] = $newItem;
        $this->dispatchItemsUpdated();
    }

    public function removeItem($index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->dispatchItemsUpdated();
        }
    }

    public function updated($property)
    {
        // Only dispatch for nested item changes that wire:model might not catch
        if (str_starts_with($property, 'items.')) {
            $this->dispatchItemsUpdated();
        }
    }

    protected function dispatchItemsUpdated(): void
    {
        // Dispatch to parent component to update the editingBlockState
        $this->dispatch('repeater-updated', [
            'model' => $this->model,
            'items' => $this->items,
        ]);
    }

    public function render()
    {
        return view('livewire.repeater');
    }
}
