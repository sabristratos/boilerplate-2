@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.heading"
        label="{{ __('blocks.features.heading_label') }}"
        placeholder="{{ __('blocks.features.heading_placeholder') }}"
    />
    
    <flux:textarea
        wire:model.live.debounce.500ms="editingBlockState.subheading"
        label="{{ __('blocks.features.subheading_label') }}"
        placeholder="{{ __('blocks.features.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.features.layout_settings_label') }}</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:select
                wire:model.live="editingBlockState.layout"
                label="{{ __('blocks.features.layout_label') }}"
            >
                <flux:select.option value="grid">{{ __('blocks.features.layout_grid') }}</flux:select.option>
                <flux:select.option value="list">{{ __('blocks.features.layout_list') }}</flux:select.option>
            </flux:select>

            <flux:select
                wire:model.live="editingBlockState.columns"
                label="{{ __('blocks.features.columns_label') }}"
            >
                <flux:select.option value="1">1 {{ __('blocks.features.column') }}</flux:select.option>
                <flux:select.option value="2">2 {{ __('blocks.features.columns') }}</flux:select.option>
                <flux:select.option value="3">3 {{ __('blocks.features.columns') }}</flux:select.option>
                <flux:select.option value="4">4 {{ __('blocks.features.columns') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.features.features_label') }}</flux:heading>
        
        <livewire:repeater
            model="editingBlockState.features"
            :items="$editingBlockState['features'] ?? []"
            :subfields="[
                'icon' => [
                    'label' => __('blocks.features.icon_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => 'star',
                    'placeholder' => __('blocks.features.icon_placeholder'),
                ],
                'title' => [
                    'label' => __('blocks.features.title_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => __('blocks.features.title_default'),
                ],
                'description' => [
                    'label' => __('blocks.features.description_label'),
                    'type' => 'textarea',
                    'required' => true,
                    'default' => __('blocks.features.description_default'),
                    'rows' => 3,
                ],
                'color' => [
                    'label' => __('blocks.features.color_label'),
                    'type' => 'select',
                    'options' => [
                        'blue' => __('blocks.features.color_blue'),
                        'green' => __('blocks.features.color_green'),
                        'yellow' => __('blocks.features.color_yellow'),
                        'red' => __('blocks.features.color_red'),
                        'purple' => __('blocks.features.color_purple'),
                        'indigo' => __('blocks.features.color_indigo'),
                        'pink' => __('blocks.features.color_pink'),
                        'orange' => __('blocks.features.color_orange'),
                    ],
                    'required' => true,
                    'default' => 'blue',
                ],
            ]"
        />
    </div>
</div> 