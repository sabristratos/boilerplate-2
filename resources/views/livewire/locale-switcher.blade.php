<div class="p-2">
    <flux:radio.group wire:model.live="currentLocale" label="Language" variant="segmented">
        @foreach($locales as $locale)
            <flux:radio
                value="{{ $locale['code'] }}"
                label="{{ strtoupper($locale['code']) }}"
                tooltip="{{ $locale['name'] }}"
            />
        @endforeach
    </flux:radio.group>
</div>
