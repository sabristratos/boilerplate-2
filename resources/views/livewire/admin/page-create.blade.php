<div>
    <form wire:submit.prevent="save">
        <flux:card class="p-0">
            <div class="p-6">
                <flux:heading>Create New Page</flux:heading>
            </div>
            <div class="p-6 space-y-6 border-t border-zinc-200 dark:border-zinc-700">
                @foreach($availableLocales as $locale => $language)
                    <fieldset class="space-y-4 p-4 border rounded-md">
                        <legend class="font-semibold">{{ $language }}</legend>
                        <flux:input
                            label="Title"
                            wire:model.live.debounce.500ms="title.{{ $locale }}"
                            wire:change="generateSlug('{{ $locale }}')"
                        />
                        <flux:input
                            label="Slug"
                            wire:model="slug.{{ $locale }}"
                        />
                    </fieldset>
                @endforeach
            </div>
            <div class="p-6 flex justify-end border-t border-zinc-200 dark:border-zinc-700">
                <flux:button type="submit" variant="primary">Save Page</flux:button>
            </div>
        </flux:card>
    </form>
</div>
