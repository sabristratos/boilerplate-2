@props(['alpine' => false])

<div>
    @php
        $forms = \App\Models\Form::all();
    @endphp

    @if ($alpine)
        <flux:textarea x-model="state.content" label="Content" rows="8" />
    @else
        <flux:textarea wire:model.defer="state.content" label="{{ __('blocks.content_area.content_label') }}" rows="8" />
    @endif

    <div class="mt-4">
        @if ($alpine)
            <flux:select x-model="state.form_id" label="{{ __('forms.block_form_label') }}" placeholder="{{ __('forms.block_form_placeholder') }}">
                @foreach($forms as $form)
                    <flux:select.option :value="$form->id">{{ $form->name }}</flux:select.option>
                @endforeach
            </flux:select>
        @else
            <flux:select wire:model.defer="state.form_id" label="{{ __('forms.block_form_label') }}" placeholder="{{ __('forms.block_form_placeholder') }}">
                @foreach($forms as $form)
                    <flux:select.option :value="$form->id">{{ $form->name }}</flux:select.option>
                @endforeach
            </flux:select>
        @endif
    </div>
</div>
