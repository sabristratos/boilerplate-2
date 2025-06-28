@props([
    'setting',
])

<div x-data="{
    items: $wire.entangle('state.{{ $setting->key }}'),
    subfields: @json($setting->subfields),
    locale: '{{ app()->getLocale() }}',
    addNewItem() {
        let newItem = {};
        Object.keys(this.subfields).forEach(key => {
            newItem[key] = '';
        });
        this.items = [...this.items, newItem];
    }
}">
    <flux:field>
        <x-slot:label>{{ __($setting->label) }}</x-slot:label>
        @if($setting->description)
            <x-slot:description>{{ __($setting->description) }}</x-slot:description>
        @endif
    </flux:field>

    <div class="mt-4 space-y-4">
        <template x-for="(item, index) in items" :key="index">
            <div class="flex items-center space-x-2">
                <div class="grid flex-1 grid-cols-2 gap-4">
                    <template x-for="(field, key) in subfields" :key="key">
                        <div>
                            <template x-if="field.type === 'select'">
                                <flux:select
                                    x-model="items[index][key]"
                                    :label="typeof field.label === 'object' ? (field.label[locale] || field.label['en']) : field.label"
                                >
                                    <template x-for="([value, label]) in Object.entries(field.options)">
                                        <option :value="value" x-text="label"></option>
                                    </template>
                                </flux:select>
                            </template>
                            <template x-if="field.type !== 'select'">
                                <flux:input
                                    x-model="items[index][key]"
                                    :label="typeof field.label === 'object' ? (field.label[locale] || field.label['en']) : field.label"
                                    :type="field.type"
                                />
                            </template>
                        </div>
                    </template>
                </div>
                <flux:button
                    x-on:click="items.splice(index, 1)"
                    icon="trash"
                    variant="ghost"
                />
            </div>
        </template>
    </div>

    <div class="mt-4">
        <flux:button
            x-on:click="addNewItem()"
            type="button"
            variant="outline"
        >
            {{ __('buttons.add') }}
        </flux:button>
    </div>
</div> 