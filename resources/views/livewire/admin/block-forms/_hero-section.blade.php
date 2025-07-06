@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.overline"
        label="{{ __('blocks.hero_section.overline_label') }}"
        placeholder="{{ __('blocks.hero_section.overline_placeholder') }}"
    />
    
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.heading"
        label="{{ __('blocks.hero_section.heading_label') }}"
        placeholder="{{ __('blocks.hero_section.heading_placeholder') }}"
        required
    />
    
    <flux:textarea
        wire:model.live.debounce.500ms="editingBlockState.subheading"
        label="{{ __('blocks.hero_section.subheading_label') }}"
        placeholder="{{ __('blocks.hero_section.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.hero_section.layout_settings_label') }}</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:select
                wire:model.live="editingBlockState.text_alignment"
                label="{{ __('blocks.hero_section.text_alignment_label') }}"
            >
                <flux:select.option value="left">{{ __('blocks.hero_section.text_alignment_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('blocks.hero_section.text_alignment_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('blocks.hero_section.text_alignment_right') }}</flux:select.option>
            </flux:select>

            <flux:select
                wire:model.live="editingBlockState.content_width"
                label="{{ __('blocks.hero_section.content_width_label') }}"
            >
                <flux:select.option value="max-w-2xl">{{ __('blocks.hero_section.content_width_small') }}</flux:select.option>
                <flux:select.option value="max-w-3xl">{{ __('blocks.hero_section.content_width_medium') }}</flux:select.option>
                <flux:select.option value="max-w-4xl">{{ __('blocks.hero_section.content_width_large') }}</flux:select.option>
                <flux:select.option value="max-w-5xl">{{ __('blocks.hero_section.content_width_xlarge') }}</flux:select.option>
                <flux:select.option value="max-w-6xl">{{ __('blocks.hero_section.content_width_2xl') }}</flux:select.option>
                <flux:select.option value="max-w-7xl">{{ __('blocks.hero_section.content_width_3xl') }}</flux:select.option>
            </flux:select>
        </div>

        <div class="mt-4">
            <flux:select
                wire:model.live="editingBlockState.padding"
                label="{{ __('blocks.hero_section.padding_label') }}"
            >
                <flux:select.option value="py-16">{{ __('blocks.hero_section.padding_small') }}</flux:select.option>
                <flux:select.option value="py-20">{{ __('blocks.hero_section.padding_medium') }}</flux:select.option>
                <flux:select.option value="py-24">{{ __('blocks.hero_section.padding_large') }}</flux:select.option>
                <flux:select.option value="py-32">{{ __('blocks.hero_section.padding_xlarge') }}</flux:select.option>
                <flux:select.option value="py-40">{{ __('blocks.hero_section.padding_2xl') }}</flux:select.option>
                <flux:select.option value="py-48">{{ __('blocks.hero_section.padding_3xl') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.hero_section.background_label') }}</flux:heading>
        
        <div class="space-y-4">
            <flux:input
                wire:model.live="editingBlockState.background_image"
                label="{{ __('blocks.hero_section.background_image_url_label') }}"
                placeholder="{{ __('blocks.hero_section.background_image_url_placeholder') }}"
                type="url"
            />
            
            <div>
                <flux:label>{{ __('blocks.hero_section.background_image_upload_label') }}</flux:label>
                <div class="mt-1">
                    @if(isset($currentBlock))
                        <livewire:media-uploader :model="$currentBlock" collection="background_image" />
                    @endif
                </div>
            </div>

            <div>
                <flux:label>{{ __('blocks.hero_section.background_overlay_label') }}</flux:label>
                <div class="mt-1">
                    <input 
                        type="range" 
                        wire:model.live="editingBlockState.background_overlay" 
                        min="0" 
                        max="100" 
                        class="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer dark:bg-zinc-700"
                    />
                    <div class="flex justify-between text-xs text-zinc-500 mt-1">
                        <span>0%</span>
                        <span>{{ $editingBlockState['background_overlay'] ?? 70 }}%</span>
                        <span>100%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.hero_section.buttons_label') }}</flux:heading>
        
        <livewire:repeater
            wire:model="editingBlockState.buttons"
            model="editingBlockState.buttons"
            :items="$editingBlockState['buttons'] ?? []"
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
