<div>
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">{{ __('navigation.database_backup') }}</flux:heading>
        
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
            <flux:breadcrumbs.item>{{ __('navigation.database_backup') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <!-- Backup Creation Section -->
    <flux:card class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">{{ __('backup.create_backup') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('backup.create_backup_description') }}
                </flux:text>
                @if(!$backupSupported)
                    <flux:text class="mt-2 text-red-600 dark:text-red-400">
                        {{ $backupSupportMessage }}
                    </flux:text>
                @endif
            </div>
            <flux:button
                wire:click="createBackup"
                :disabled="$isCreatingBackup || !$backupSupported"
                :loading="$isCreatingBackup"
                icon="cloud-arrow-up"
            >
                {{ $isCreatingBackup ? __('backup.creating_backup') : __('backup.create_backup') }}
            </flux:button>
        </div>
    </flux:card>

    <!-- Backup History Section -->
    <flux:card>
        <div class="mb-4">
            <flux:heading size="lg">{{ __('backup.backup_history') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('backup.backup_history_description') }}
            </flux:text>
        </div>

        @if($this->backups->count() > 0)
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                {{ __('backup.filename') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                {{ __('backup.size') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                {{ __('backup.created_at') }}
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">{{ __('buttons.actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($this->backups as $backup)
                            <tr wire:key="backup-{{ $backup['name'] }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <flux:icon name="document-arrow-down" class="size-5 text-zinc-400 mr-3" />
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $backup['name'] }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                                    {{ $backup['size'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                                    {{ $backup['date_formatted'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            wire:click="downloadBackup('{{ $backup['name'] }}')"
                                            variant="ghost"
                                            size="xs"
                                            icon="arrow-down-tray"
                                            square
                                            tooltip="{{ __('backup.download') }}"
                                        />
                                        <flux:button
                                            wire:click="confirmDeleteBackup('{{ $backup['name'] }}')"
                                            variant="danger"
                                            size="xs"
                                            icon="trash"
                                            square
                                            tooltip="{{ __('backup.delete') }}"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <flux:callout icon="information-circle" variant="secondary">
                <flux:callout.heading>{{ __('backup.no_backups_found') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('backup.no_backups_found_description') }}
                </flux:callout.text>
            </flux:callout>
        @endif
    </flux:card>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model.live.self="showDeleteModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('backup.delete_confirm_title') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('backup.delete_confirm_text', ['filename' => $backupToDelete]) }}
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button
                    wire:click="cancelDelete"
                    variant="outline"
                >
                    {{ __('buttons.cancel') }}
                </flux:button>
                <flux:button
                    wire:click="deleteBackup"
                    variant="danger"
                >
                    {{ __('backup.delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div> 