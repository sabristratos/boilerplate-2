@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.heading"
        label="{{ __('blocks.call_to_action.heading_label') }}"
        placeholder="{{ __('blocks.call_to_action.heading_placeholder') }}"
        required
    />
    
    <flux:textarea
        wire:model.live.debounce.500ms="editingBlockState.subheading"
        label="{{ __('blocks.call_to_action.subheading_label') }}"
        placeholder="{{ __('blocks.call_to_action.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.call_to_action.styling_label') }}</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:select
                wire:model.live="editingBlockState.background_color"
                label="{{ __('blocks.call_to_action.background_color_label') }}"
            >
                <flux:select.option value="white">{{ __('blocks.call_to_action.background_white') }}</flux:select.option>
                <flux:select.option value="gray">{{ __('blocks.call_to_action.background_gray') }}</flux:select.option>
                <flux:select.option value="primary">{{ __('blocks.call_to_action.background_primary') }}</flux:select.option>
                <flux:select.option value="secondary">{{ __('blocks.call_to_action.background_secondary') }}</flux:select.option>
            </flux:select>

            <flux:select
                wire:model.live="editingBlockState.text_alignment"
                label="{{ __('blocks.call_to_action.text_alignment_label') }}"
            >
                <flux:select.option value="left">{{ __('blocks.call_to_action.alignment_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('blocks.call_to_action.alignment_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('blocks.call_to_action.alignment_right') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.call_to_action.buttons_label') }}</flux:heading>
        
        <livewire:repeater
            model="editingBlockState.buttons"
            :items="$editingBlockState['buttons'] ?? []"
            :subfields="[
                'text' => [
                    'label' => __('blocks.call_to_action.button_text_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => __('blocks.call_to_action.button_text_default'),
                ],
                'url' => [
                    'label' => __('blocks.call_to_action.button_url_label'),
                    'type' => 'url',
                    'required' => true,
                    'default' => '#',
                ],
                'variant' => [
                    'label' => __('blocks.call_to_action.button_variant_label'),
                    'type' => 'select',
                    'options' => [
                        'primary' => __('blocks.call_to_action.button_variant_primary'),
                        'secondary' => __('blocks.call_to_action.button_variant_secondary'),
                        'ghost' => __('blocks.call_to_action.button_variant_ghost'),
                    ],
                    'required' => true,
                    'default' => 'primary',
                ],
            ]"
        />
    </div>
</div> 