@props(['page', 'availableLocales', 'activeLocale', 'switchLocale', 'isPublished'])

<div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50">
    <!-- Left Section: Navigation & Page Info -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pages.index') }}" wire:navigate class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors">
            <flux:icon name="arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">{{ __('navigation.pages') }}</span>
        </a>
        <div class="w-px h-4 bg-zinc-300 dark:bg-zinc-600"></div>
        <div>
            <h1 class="text-sm font-medium text-zinc-900 dark:text-white">
                {{ $page->getTranslation('title', app()->getLocale()) ?: __('messages.page_manager.untitled_page') }}
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('messages.page_manager.page_builder') }}</p>
        </div>
    </div>
    
    <!-- Center Section: Canvas Info -->
    <div class="flex items-center gap-4">
        <flux:heading size="sm">{{ __('messages.page_manager.page_canvas') }}</flux:heading>
        <flux:text variant="subtle" class="text-sm">{{ __('messages.page_manager.auto_save_enabled') }}</flux:text>
    </div>
    
    <!-- Right Section: Controls -->
    <div class="flex items-center gap-4">
        <!-- Language Switcher -->
        @if(count($availableLocales) > 1)
            <div class="flex items-center gap-2">
                <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('messages.page_manager.language') }}</flux:label>
                <flux:radio.group wire:model.live="switchLocale" variant="segmented" size="sm">
                    @foreach($availableLocales as $localeCode => $localeName)
                        <flux:radio value="{{ $localeCode }}" label="{{ $localeName }}" />
                    @endforeach
                </flux:radio.group>
            </div>
        @endif

        <!-- Publishing Switch -->
        <div class="flex items-center gap-2">
            <flux:field variant="inline">
                <flux:switch wire:model.live="isPublished" />
                <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('messages.page_manager.status') }}</flux:label>
            </flux:field>
        </div>

        <!-- Preview Button -->
        <flux:button
            href="{{ route('pages.show', $page) }}"
            target="_blank"
            icon="eye"
            variant="subtle"
            size="sm"
        >
            {{ __('messages.page_manager.preview') }}
        </flux:button>
    </div>
</div> 