<div>
    @php
        $forms = \App\Models\Form::all();
    @endphp

    <flux:textarea wire:model.live="state.content" label="{{ __('blocks.content_area.content_label') }}" rows="8" />

    <div class="mt-4">
        <flux:select wire:model.live="state.form_id" label="{{ __('forms.block_form_label') }}" placeholder="{{ __('forms.block_form_placeholder') }}">
            @foreach($forms as $form)
                <flux:select.option :value="$form->id">{{ $form->getTranslation('name', app()->getLocale()) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>
</div>
