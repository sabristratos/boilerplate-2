<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Model;

class SettingRepeater extends Component
{
    #[Model]
    public array $items = [];
    public array $fields = [];

    public function mount(array $fields = [], array $items = [])
    {
        $this->fields = $fields;
        $this->items = $items ?? [];
    }

    public function addItem()
    {
        $newItem = [];
        foreach ($this->fields as $field) {
            $newItem[$field['name']] = '';
        }
        $this->items[] = $newItem;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function render()
    {
        return view('livewire.setting-repeater');
    }
} 