<?php

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

    public function updatedItems(): void
    {
        $this->dispatchItemsUpdated();
    }

    protected function dispatchItemsUpdated()
    {
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
