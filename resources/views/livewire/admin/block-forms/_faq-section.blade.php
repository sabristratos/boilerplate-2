@props(['alpine' => false])

<div class="space-y-4">
    <flux:input 
        wire:model.live="state.heading" 
        label="{{ __('blocks.faq_section.heading_label') }}" 
        placeholder="{{ __('blocks.faq_section.heading_placeholder') }}"
    />
    
    <flux:textarea 
        wire:model.live="state.subheading" 
        label="{{ __('blocks.faq_section.subheading_label') }}" 
        placeholder="{{ __('blocks.faq_section.subheading_placeholder') }}"
        rows="2"
    />

    <div class="grid grid-cols-2 gap-4">
        <flux:select wire:model.live="state.style" label="{{ __('blocks.faq_section.style_label') }}">
            <flux:select.option value="accordion">{{ __('blocks.faq_section.style_accordion') }}</flux:select.option>
            <flux:select.option value="list">{{ __('blocks.faq_section.style_list') }}</flux:select.option>
        </flux:select>
        
        <flux:field variant="inline">
            <flux:switch wire:model.live="state.expand_first" />
            <flux:label>{{ __('blocks.faq_section.expand_first_label') }}</flux:label>
        </flux:field>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.faq_section.items_label') }}</flux:heading>

        <div class="space-y-4">
            <template x-for="(faq, index) in state.faqs" :key="index">
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg space-y-3 relative bg-white dark:bg-zinc-800">
                    <div class="absolute top-3 right-3">
                        <flux:button 
                            @click="state.faqs.splice(index, 1)" 
                            size="xs" 
                            variant="danger" 
                            icon="trash" 
                            :tooltip="__('blocks.faq_section.remove_item')"
                        />
                    </div>
                    <flux:input 
                        x-model="faq.question" 
                        label="{{ __('blocks.faq_section.question_label') }}" 
                        placeholder="{{ __('blocks.faq_section.question_placeholder') }}"
                    />
                    <flux:textarea 
                        x-model="faq.answer" 
                        label="{{ __('blocks.faq_section.answer_label') }}" 
                        placeholder="{{ __('blocks.faq_section.answer_placeholder') }}"
                        rows="3"
                    />
                </div>
            </template>
        </div>

        <div class="mt-4">
            <flux:button 
                @click="state.faqs.push({question: '', answer: ''})" 
                variant="outline" 
                icon="plus"
            >
                {{ __('blocks.faq_section.add_item_button') }}
            </flux:button>
        </div>
    </div>
</div>
