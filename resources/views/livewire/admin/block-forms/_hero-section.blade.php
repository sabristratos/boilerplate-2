@props(['alpine' => false])

<div>
    <div class="space-y-4">
        <div>
            <flux:input wire:model.live="state.overline" label="Overline" />
        </div>
        <div>
            <flux:input wire:model.live="state.heading" label="Heading" />
        </div>
        <div>
            <flux:textarea wire:model.live="state.subheading" label="Subheading" rows="3" />
        </div>

            <h3 class="text-lg font-medium">Buttons</h3>
            <div class="mt-4">
                <livewire:repeater
                    :items="$state['buttons'] ?? []"
                    :subfields="[
                        'text' => ['type' => 'text', 'label' => 'Text'],
                        'url' => ['type' => 'url', 'label' => 'URL'],
                        'variant' => [
                            'type' => 'select',
                            'label' => 'Variant',
                            'options' => [
                                'primary' => 'Primary',
                                'secondary' => 'Secondary',
                                'ghost' => 'Ghost',
                            ]
                        ]
                    ]"
                    model="state.buttons"
                    :locale="$activeLocale"
                />
        </div>

        <div>
            <flux:label>{{ __('blocks.hero_section.image_label') }}</flux:label>
            <div class="mt-1">
                <livewire:media-uploader :model="$editingBlock" collection="image" />
            </div>
        </div>
    </div>
</div>
