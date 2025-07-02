@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.heading"
        label="{{ __('blocks.faq_section.heading_label') }}"
        placeholder="{{ __('blocks.faq_section.heading_placeholder') }}"
    />
    
    <flux:textarea
        wire:model.live.debounce.500ms="editingBlockState.subheading"
        label="{{ __('blocks.faq_section.subheading_label') }}"
        placeholder="{{ __('blocks.faq_section.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.faq_section.styling_label') }}</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:select wire:model.live="editingBlockState.style" label="{{ __('blocks.faq_section.style_label') }}">
                <flux:select.option value="accordion">{{ __('blocks.faq_section.style_accordion') }}</flux:select.option>
                <flux:select.option value="list">{{ __('blocks.faq_section.style_list') }}</flux:select.option>
            </flux:select>

            <flux:select
                wire:model.live="editingBlockState.text_alignment"
                label="{{ __('blocks.faq_section.text_alignment_label') }}"
            >
                <flux:select.option value="left">{{ __('blocks.faq_section.alignment_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('blocks.faq_section.alignment_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('blocks.faq_section.alignment_right') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.faq_section.layout_label') }}</flux:heading>
        
        <flux:select
            wire:model.live="editingBlockState.max_width"
            label="{{ __('blocks.faq_section.max_width_label') }}"
        >
            <flux:select.option value="sm">Small (640px)</flux:select.option>
            <flux:select.option value="md">Medium (768px)</flux:select.option>
            <flux:select.option value="lg">Large (1024px)</flux:select.option>
            <flux:select.option value="xl">Extra Large (1280px)</flux:select.option>
            <flux:select.option value="full">Full Width</flux:select.option>
        </flux:select>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.faq_section.appearance_label') }}</flux:heading>
        
        <flux:select
            wire:model.live="editingBlockState.background_color"
            label="{{ __('blocks.faq_section.background_color_label') }}"
        >
            <flux:select.option value="white">{{ __('blocks.faq_section.background_white') }}</flux:select.option>
            <flux:select.option value="gray">{{ __('blocks.faq_section.background_gray') }}</flux:select.option>
            <flux:select.option value="primary">{{ __('blocks.faq_section.background_primary') }}</flux:select.option>
            <flux:select.option value="secondary">{{ __('blocks.faq_section.background_secondary') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.faq_section.behavior_label') }}</flux:heading>
        
        <div class="space-y-4">
            <flux:field variant="inline">
                <flux:switch wire:model.live="editingBlockState.expand_first" />
                <flux:label>{{ __('blocks.faq_section.expand_first_label') }}</flux:label>
            </flux:field>

            <flux:field variant="inline">
                <flux:switch wire:model.live="editingBlockState.show_icons" />
                <flux:label>{{ __('blocks.faq_section.show_icons_label') }}</flux:label>
            </flux:field>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.faq_section.faqs_label') }}</flux:heading>
        
        <livewire:repeater
            model="editingBlockState.faqs"
            :items="$editingBlockState['faqs'] ?? []"
            :subfields="[
                'question' => [
                    'label' => __('blocks.faq_section.question_label'),
                    'type' => 'text',
                    'required' => true,
                    'default' => __('blocks.faq_section.question_default'),
                ],
                'answer' => [
                    'label' => __('blocks.faq_section.answer_label'),
                    'type' => 'textarea',
                    'required' => true,
                    'default' => __('blocks.faq_section.answer_default'),
                    'rows' => 3,
                ],
            ]"
        />
    </div>
</div>
