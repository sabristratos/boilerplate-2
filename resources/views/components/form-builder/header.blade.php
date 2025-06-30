@props(['form'])
<div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
    <a href="{{ route('admin.forms.index') }}" wire:navigate class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
            <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm font-medium">{{ __('navigation.forms') }}</span>
    </a>
    <div class="flex justify-between items-start mb-4">
        <div>
            <flux:heading size="lg">{{ $form->getTranslation('name', 'en') }}</flux:heading>
            <flux:text variant="subtle">ID: {{ $form->id }}</flux:text>
        </div>
        <flux:button 
            href="{{ route('admin.forms.submissions', $form) }}" 
            wire:navigate
            size="sm"
            variant="ghost"
            icon="document-text"
            tooltip="View form submissions"
        >
            Submissions ({{ $form->submissions()->count() }})
        </flux:button>
    </div>
</div> 