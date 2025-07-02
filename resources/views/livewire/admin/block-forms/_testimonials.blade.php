@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.heading"
        label="{{ __('blocks.testimonials.heading_label') }}"
        placeholder="{{ __('blocks.testimonials.heading_placeholder') }}"
    />
    
    <flux:textarea
        wire:model.live.debounce.500ms="editingBlockState.subheading"
        label="{{ __('blocks.testimonials.subheading_label') }}"
        placeholder="{{ __('blocks.testimonials.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.testimonials.layout_settings_label') }}</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:select
                wire:model.live="editingBlockState.layout"
                label="{{ __('blocks.testimonials.layout_label') }}"
            >
                <flux:select.option value="grid">{{ __('blocks.testimonials.layout_grid') }}</flux:select.option>
                <flux:select.option value="carousel">{{ __('blocks.testimonials.layout_carousel') }}</flux:select.option>
            </flux:select>

            <flux:select
                wire:model.live="editingBlockState.columns"
                label="{{ __('blocks.testimonials.columns_label') }}"
            >
                <flux:select.option value="1">1 {{ __('blocks.testimonials.column') }}</flux:select.option>
                <flux:select.option value="2">2 {{ __('blocks.testimonials.columns') }}</flux:select.option>
                <flux:select.option value="3">3 {{ __('blocks.testimonials.columns') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.testimonials.display_options_label') }}</flux:heading>
        
        <div class="space-y-4">
            <flux:field variant="inline">
                <flux:switch wire:model.live="editingBlockState.show_avatars" />
                <flux:label>{{ __('blocks.testimonials.show_avatars_label') }}</flux:label>
            </flux:field>

            <flux:field variant="inline">
                <flux:switch wire:model.live="editingBlockState.show_ratings" />
                <flux:label>{{ __('blocks.testimonials.show_ratings_label') }}</flux:label>
            </flux:field>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.testimonials.testimonials_label') }}</flux:heading>
        
        <livewire:repeater
            model="editingBlockState.testimonials"
            :items="$editingBlockState['testimonials'] ?? []"
            :subfields="[
                'name' => [
                    'label' => __('blocks.testimonials.name_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => __('blocks.testimonials.name_default'),
                ],
                'role' => [
                    'label' => __('blocks.testimonials.role_label'),
                    'type' => 'text',
                    'required' => false,
                    'default' => __('blocks.testimonials.role_default'),
                ],
                'content' => [
                    'label' => __('blocks.testimonials.content_label'),
                    'type' => 'textarea',
                    'required' => true,
                    'default' => __('blocks.testimonials.content_default'),
                    'rows' => 3,
                ],
                'rating' => [
                    'label' => __('blocks.testimonials.rating_label'),
                    'type' => 'number',
                    'required' => false,
                    'default' => 5,
                    'min' => 1,
                    'max' => 5,
                ],
                'avatar' => [
                    'label' => __('blocks.testimonials.avatar_label'),
                    'type' => 'image',
                    'required' => false,
                ],
            ]"
        />
    </div>
</div> 