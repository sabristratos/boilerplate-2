@props(['alpine' => false])

<div class="space-y-4">
    <flux:input
        wire:model.live.debounce.500ms="editingBlockState.heading"
        label="{{ __('blocks.contact.heading_label') }}"
        placeholder="{{ __('blocks.contact.heading_placeholder') }}"
    />
    
    <flux:textarea
        wire:model.live.debounce.500ms="editingBlockState.subheading"
        label="{{ __('blocks.contact.subheading_label') }}"
        placeholder="{{ __('blocks.contact.subheading_placeholder') }}"
        rows="3"
    />

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.contact.form_settings_label') }}</flux:heading>
        
        <flux:select
            wire:model.live="editingBlockState.form_id"
            label="{{ __('blocks.contact.form_select_label') }}"
            placeholder="{{ __('blocks.contact.form_select_placeholder') }}"
        >
            <flux:select.option value="">{{ __('blocks.contact.form_select_none') }}</flux:select.option>
            @foreach(\App\Models\Form::all() as $form)
                <flux:select.option value="{{ $form->id }}">{{ $form->getTranslation('name', app()->getLocale()) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.contact.styling_label') }}</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:select
                wire:model.live="editingBlockState.background_color"
                label="{{ __('blocks.contact.background_color_label') }}"
            >
                <flux:select.option value="white">{{ __('blocks.contact.background_white') }}</flux:select.option>
                <flux:select.option value="gray">{{ __('blocks.contact.background_gray') }}</flux:select.option>
                <flux:select.option value="primary">{{ __('blocks.contact.background_primary') }}</flux:select.option>
                <flux:select.option value="secondary">{{ __('blocks.contact.background_secondary') }}</flux:select.option>
            </flux:select>

            <flux:select
                wire:model.live="editingBlockState.text_alignment"
                label="{{ __('blocks.contact.text_alignment_label') }}"
            >
                <flux:select.option value="left">{{ __('blocks.contact.alignment_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('blocks.contact.alignment_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('blocks.contact.alignment_right') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
        <flux:heading size="sm" class="mb-4">{{ __('blocks.contact.contact_info_label') }}</flux:heading>
        
        <flux:field variant="inline">
            <flux:switch wire:model.live="editingBlockState.show_contact_info" />
            <flux:label>{{ __('blocks.contact.show_contact_info_label') }}</flux:label>
        </flux:field>

        @if(($editingBlockState['show_contact_info'] ?? false))
            <div class="mt-4 space-y-4">
                <flux:input
                    wire:model.live.debounce.500ms="editingBlockState.contact_info.email"
                    label="{{ __('blocks.contact.email_label') }}"
                    placeholder="{{ __('blocks.contact.email_placeholder') }}"
                    type="email"
                />
                
                <flux:input
                    wire:model.live.debounce.500ms="editingBlockState.contact_info.phone"
                    label="{{ __('blocks.contact.phone_label') }}"
                    placeholder="{{ __('blocks.contact.phone_placeholder') }}"
                />
                
                <flux:textarea
                    wire:model.live.debounce.500ms="editingBlockState.contact_info.address"
                    label="{{ __('blocks.contact.address_label') }}"
                    placeholder="{{ __('blocks.contact.address_placeholder') }}"
                    rows="2"
                />
            </div>
        @endif
    </div>
</div> 