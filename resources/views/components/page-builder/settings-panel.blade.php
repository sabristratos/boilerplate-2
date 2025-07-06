@props(['activeLocale'])

<div class="p-5 space-y-6">

    <!-- Page Title -->
    <flux:input
        wire:model.live="title.{{ $activeLocale }}"
        label="{{ __('messages.page_manager.title_label') }}"
        description="{{ __('messages.page_manager.title_help') }}"
    />

    <!-- Page Slug -->
    <flux:field>
        <x-slot name="label">
            <div class="flex items-center gap-x-2">
                <flux:label>{{ __('messages.page_manager.slug_label') }}</flux:label>
                <flux:tooltip toggleable>
                    <flux:button icon="information-circle" size="sm" variant="ghost" />
                    <flux:tooltip.content class="max-w-[20rem]">
                        {{ __('messages.page_manager.slug_tooltip') }}
                    </flux:tooltip.content>
                </flux:tooltip>
            </div>
        </x-slot>
        <flux:input.group>
            <flux:input wire:model.live="slug" />
            <flux:button 
                tooltip="{{ __('messages.page_manager.generate_slug_tooltip') }}"
                x-on:click.prevent="$wire.generateSlug()"
            >
                {{ __('messages.page_manager.generate_slug_button') }}
            </flux:button>
        </flux:input.group>
    </flux:field>

    <flux:separator />

    <!-- SEO Settings -->
    <div>
        <flux:heading size="sm">{{ __('messages.page_manager.seo') }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:input
                wire:model.live="meta_title.{{ $activeLocale }}"
                label="{{ __('messages.page_manager.meta_title') }}"
            />
            <flux:textarea
                wire:model.live="meta_description.{{ $activeLocale }}"
                label="{{ __('messages.page_manager.meta_description') }}"
            />
            <div class="flex items-center gap-x-2">
                <flux:switch
                    wire:model.live="no_index"
                    label="{{ __('messages.page_manager.no_index_label') }}"
                />
                <flux:tooltip toggleable>
                    <flux:button icon="information-circle" size="sm" variant="ghost" />
                    <flux:tooltip.content class="max-w-[20rem]">
                        {{ __('messages.page_manager.visibility_tooltip') }}
                    </flux:tooltip.content>
                </flux:tooltip>
            </div>
        </div>
    </div>
</div> 