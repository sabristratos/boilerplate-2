<?php

namespace App\Livewire;

use Livewire\Component;

class SettingRepeater extends Component
{
    public string $settingKey;
    public array $subfields;
    public array $items = [];

    public function mount(string $settingKey, array $subfields, ?array $value = [])
    {
        $this->settingKey = $settingKey;
        $this->subfields = $subfields;
        $this->items = $value ?? [];
    }

    public function addItem()
    {
        $newItem = [];
        foreach (array_keys($this->subfields) as $key) {
            $newItem[$key] = '';
        }
        $this->items[] = $newItem;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->updateParent();
    }

    public function updatedItems()
    {
        $this->updateParent();
    }

    protected function updateParent()
    {
        $this->dispatch('repeater-updated', settingKey: $this->settingKey, items: $this->items);
    }

    public function render()
    {
        return view('livewire.setting-repeater');
    }
} 