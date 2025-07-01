<div
    x-data="{
        liveState: null,
        editingBlockId: null,
    }"
    @block-state-updated.window="
        editingBlockId = $event.detail.id;
        liveState = $event.detail.state;
    "
    @block-was-updated.window="
        editingBlockId = null;
        liveState = null;
    "
    class="flex flex-col h-screen bg-zinc-100 dark:bg-zinc-900 font-sans"
>
    <!-- Unified Header -->
    <x-page-builder.header 
        :page="$page" 
        :availableLocales="$availableLocales" 
        :activeLocale="$activeLocale" 
        :switchLocale="$switchLocale" 
        :isPublished="$isPublished"
    />

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Left Panel: Toolbox & Settings -->
        <div class="w-96 bg-white dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700/50 flex flex-col overflow-visible">
            <x-page-builder.toolbox 
                :tab="$tab" 
                :activeLocale="$activeLocale" 
                :blockManager="$this->blockManager"
            />
        </div>

        <!-- Center Panel: Canvas -->
        <div class="flex-1 flex flex-col">
            <x-page-builder.page-canvas 
                :blocks="$this->blocks" 
                :editingBlockId="$editingBlockId" 
                :editingBlockState="$editingBlockState" 
                :blockManager="$this->blockManager"
            />
        </div>

        <!-- Right Panel: Properties -->
        <div class="w-[400px] bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 overflow-y-auto shrink-0">
            <div class="p-6">
                <livewire:admin.block-editor :state="$editingBlockState" :active-locale="$activeLocale" />
            </div>
        </div>
    </div>
</div>
