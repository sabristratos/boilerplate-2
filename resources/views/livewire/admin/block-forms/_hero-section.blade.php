@props(['alpine' => false])

<div>
    <div class="space-y-4">
        <x-flux::input
            wire:model.defer="state.overline"
            :label="__('labels.overline')"
        />
        <x-flux::input
            wire:model.defer="state.heading"
            :label="__('labels.heading')"
            :required="true"
        />
        <x-flux::textarea
            wire:model.defer="state.subheading"
            :label="__('labels.subheading')"
        />
        <livewire:repeater
            model="state.buttons"
            :items="$state['buttons'] ?? []"
            :subfields="[
                'text' => [
                    'label' => 'Button Text',
                    'type' => 'text',
                    'required' => true,
                    'default' => 'Click me',
                ],
                'url' => [
                    'label' => 'Button URL',
                    'type' => 'text',
                    'required' => true,
                    'default' => '#',
                ],
                'variant' => [
                    'label' => 'Button Variant',
                    'type' => 'select',
                    'options' => [
                        'primary' => 'Primary',
                        'outline' => 'Outline',
                    ],
                    'required' => true,
                    'default' => 'primary',
                ],
            ]"
        />
        <div>
            <flux:label>{{ __('blocks.hero_section.image_label') }}</flux:label>
            <div class="mt-1">
                <livewire:media-uploader :model="$editingBlock" collection="image" />
            </div>
        </div>
    </div>
</div>
