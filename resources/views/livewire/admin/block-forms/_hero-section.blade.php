@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live="state.overline"
        label="{{ __('blocks.hero_section.overline_label') }}"
        placeholder="{{ __('blocks.hero_section.overline_placeholder') }}"
    />
    
    <flux:input
        wire:model.live="state.heading"
        label="{{ __('blocks.hero_section.heading_label') }}"
        placeholder="{{ __('blocks.hero_section.heading_placeholder') }}"
        required
    />
    
    <flux:textarea
        wire:model.live="state.subheading"
        label="{{ __('blocks.hero_section.subheading_label') }}"
        placeholder="{{ __('blocks.hero_section.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.hero_section.background_label') }}</flux:heading>
        
        <div class="space-y-4">
            <flux:input
                wire:model.live="state.background_image"
                label="{{ __('blocks.hero_section.background_image_url_label') }}"
                placeholder="{{ __('blocks.hero_section.background_image_url_placeholder') }}"
                type="url"
            />
            
            <div>
                <flux:label>{{ __('blocks.hero_section.background_image_upload_label') }}</flux:label>
                <div class="mt-1">
                    <livewire:media-uploader :model="$editingBlock" collection="background_image" />
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.hero_section.buttons_label') }}</flux:heading>
        
        <livewire:repeater
            model="state.buttons"
            :items="$state['buttons'] ?? []"
            :subfields="[
                'text' => [
                    'label' => __('blocks.hero_section.button_text_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => __('blocks.hero_section.button_text_default'),
                ],
                'url' => [
                    'label' => __('blocks.hero_section.button_url_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => '#',
                ],
                'variant' => [
                    'label' => __('blocks.hero_section.button_variant_label'),
                    'type' => 'select',
                    'options' => [
                        'primary' => __('blocks.hero_section.button_variant_primary'),
                        'secondary' => __('blocks.hero_section.button_variant_secondary'),
                        'ghost' => __('blocks.hero_section.button_variant_ghost'),
                    ],
                    'required' => true,
                    'default' => 'primary',
                ],
            ]"
        />
    </div>
</div>
