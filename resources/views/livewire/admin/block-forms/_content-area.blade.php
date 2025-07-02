<div class="space-y-4">
    <flux:textarea 
        wire:model.live="editingBlockState.content" 
        label="{{ __('blocks.content_area.content_label') }}" 
        rows="8" 
        placeholder="{{ __('blocks.content_area.content_placeholder') }}"
    />

    <flux:field variant="inline">
        <flux:switch wire:model.live="editingBlockState.show_form" />
        <flux:label>{{ __('blocks.content_area.show_form_label') }}</flux:label>
    </flux:field>

    @if($editingBlockState['show_form'] ?? false)
        <div class="space-y-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
            @php
                $forms = \App\Models\Form::all();
            @endphp

            <flux:select 
                wire:model.live="editingBlockState.form_id" 
                label="{{ __('forms.block_form_label') }}" 
                placeholder="{{ __('forms.block_form_placeholder') }}"
            >
                <flux:select.option value="">{{ __('forms.select_form_placeholder') }}</flux:select.option>
                @foreach($forms as $form)
                    <flux:select.option :value="$form->id">
                        {{ $form->getTranslation('name', app()->getLocale()) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select 
                wire:model.live="editingBlockState.form_position" 
                label="{{ __('blocks.content_area.form_position_label') }}"
            >
                <flux:select.option value="top">{{ __('blocks.content_area.form_position_top') }}</flux:select.option>
                <flux:select.option value="bottom">{{ __('blocks.content_area.form_position_bottom') }}</flux:select.option>
                <flux:select.option value="inline">{{ __('blocks.content_area.form_position_inline') }}</flux:select.option>
            </flux:select>
        </div>
    @endif
</div>
