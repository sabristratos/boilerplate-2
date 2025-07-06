<div class="flex flex-col h-screen bg-zinc-100 dark:bg-zinc-900 font-sans">
    <!-- Unified Header -->
    <x-page-builder.header 
        :page="$page" 
        :availableLocales="$availableLocales" 
        :activeLocale="$activeLocale" 
        :switchLocale="$switchLocale" 
    />

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Left Panel: Toolbox & Settings -->
        <div class="w-96 bg-white dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700/50 flex flex-col overflow-visible">
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

                <flux:tab.panel name="settings">
                    <x-page-builder.settings-panel 
                        :activeLocale="$activeLocale"
                    />
                </flux:tab.panel>

                <flux:tab.panel name="add">
                    <livewire:admin.block-library 
                        :page="$page" 
                        :availableLocales="$availableLocales"
                        :key="'block-library-' . $page->id"
                    />
                </flux:tab.panel>
            </flux:tab.group>
        </div>

        <!-- Center Panel: Canvas -->
        <div class="flex-1 flex flex-col">
            <livewire:admin.page-canvas 
                :page="$page" 
                :activeLocale="$activeLocale"
                :key="'page-canvas-' . $page->id"
            />
        </div>

        <!-- Right Panel: Block Editor -->
        <div class="w-[400px] bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 overflow-y-auto shrink-0">
            <livewire:admin.block-editor 
                :page="$page"
                :activeLocale="$activeLocale"
                :key="'block-editor-' . $page->id"
            />
        </div>
    </div>

    <!-- Confirmation Modal -->
    <livewire:confirmation-modal />
</div>
