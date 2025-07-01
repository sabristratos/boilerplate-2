@props(['tab', 'activeLocale', 'blockManager'])

<div class="flex-1 overflow-y-auto">
    <flux:tab.group wire:model.live="tab">
        <flux:tabs class="grid grid-cols-2">
            <flux:tab name="settings" class="flex justify-center" icon="cog-6-tooth">
                <flux:tooltip content="Page title, slug, and general settings">
                    Settings
                </flux:tooltip>
            </flux:tab>
            <flux:tab name="add" class="flex justify-center" icon="plus">
                <flux:tooltip content="Add new content blocks to your page">
                    Add
                </flux:tooltip>
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="settings" class="p-5">
            <x-page-builder.settings-panel 
                :activeLocale="$activeLocale"
            />
        </flux:tab.panel>

        <flux:tab.panel name="add" class="p-5">
            <x-page-builder.block-library :blockManager="$blockManager" />
        </flux:tab.panel>
    </flux:tab.group>
</div> 