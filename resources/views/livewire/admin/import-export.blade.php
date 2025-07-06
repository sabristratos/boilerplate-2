<div>
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">{{ __('messages.import_export.title') }}</flux:heading>
        
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
            <flux:breadcrumbs.item>{{ __('messages.import_export.title') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <flux:tab.group>
        <flux:tabs wire:model="activeTab">
            <flux:tab name="export">{{ __('messages.import_export.export_tab') }}</flux:tab>
            <flux:tab name="import">{{ __('messages.import_export.import_tab') }}</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="export">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('messages.import_export.export_heading') }}</flux:heading>
                
                <div class="space-y-6">
                    <!-- Export Type Selection -->
                    <flux:field>
                        <flux:label>{{ __('messages.import_export.export_type') }}</flux:label>
                        <flux:radio.group wire:model.live="selectedType">
                            <flux:radio value="resources" label="{{ __('messages.import_export.resources') }}" />
                            <flux:radio value="pages" label="{{ __('messages.import_export.pages') }}" />
                            <flux:radio value="forms" label="{{ __('messages.import_export.forms') }}" />
                        </flux:radio.group>
                    </flux:field>

                    <!-- Resource Selection (only for resources type) -->
                    @if($selectedType === 'resources')
                        <flux:field>
                            <flux:label>{{ __('messages.import_export.select_resource') }}</flux:label>
                            <flux:select wire:model.live="selectedResource" placeholder="{{ __('messages.import_export.choose_resource') }}">
                                @foreach($this->resources as $resource)
                                    <flux:select.option value="{{ get_class($resource) }}">
                                        {{ $resource::pluralLabel() }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    @endif

                    <!-- Item Selection -->
                    @if($selectedResource || $selectedType !== 'resources')
                        <flux:field>
                            <flux:label>{{ __('messages.import_export.select_items') }}</flux:label>
                            <div class="flex gap-2 mb-3">
                                <flux:button wire:click="selectAll" variant="outline" size="sm">
                                    {{ __('messages.import_export.select_all') }}
                                </flux:button>
                                <flux:button wire:click="deselectAll" variant="outline" size="sm">
                                    {{ __('messages.import_export.deselect_all') }}
                                </flux:button>
                            </div>
                            
                            <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-2">
                                @forelse($this->resourceData as $item)
                                    <flux:field variant="inline">
                                        <flux:checkbox 
                                            wire:model.live="selectedIds" 
                                            value="{{ $item['id'] }}" 
                                            id="item_{{ $item['id'] }}"
                                        />
                                        <flux:label for="item_{{ $item['id'] }}">{{ $item['name'] }}</flux:label>
                                    </flux:field>
                                @empty
                                    <flux:text variant="subtle" class="text-center py-4">
                                        {{ __('messages.import_export.no_items_found') }}
                                    </flux:text>
                                @endforelse
                            </div>
                        </flux:field>

                        <!-- Export Options -->
                        <flux:field variant="inline">
                            <flux:switch wire:model="includeMedia" />
                            <flux:label>{{ __('messages.import_export.include_media') }}</flux:label>
                        </flux:field>

                        <!-- Export Button -->
                        <div class="flex justify-end">
                            <flux:button 
                                wire:click="export" 
                                variant="primary"
                                icon="arrow-down-tray"
                                :disabled="empty($selectedIds)"
                            >
                                {{ __('messages.import_export.export_button') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            </flux:card>
        </flux:tab.panel>

        <flux:tab.panel name="import">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('messages.import_export.import_heading') }}</flux:heading>
                
                <div class="space-y-6">
                    <!-- Import File Upload -->
                    <flux:field>
                        <flux:label>{{ __('messages.import_export.import_file') }}</flux:label>
                        <flux:description>{{ __('messages.import_export.import_file_help') }}</flux:description>
                        <flux:input 
                            type="file" 
                            wire:model="importFile" 
                            accept=".zip,.json"
                        />
                        @error('importFile') 
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <!-- Import Options -->
                    <flux:field variant="inline">
                        <flux:switch wire:model="overwriteExisting" />
                        <flux:label>{{ __('messages.import_export.overwrite_existing') }}</flux:label>
                    </flux:field>

                    <!-- Import Button -->
                    <div class="flex justify-end">
                        <flux:button 
                            wire:click="import" 
                            variant="primary"
                            icon="arrow-up-tray"
                            :disabled="!$importFile"
                        >
                            {{ __('messages.import_export.import_button') }}
                        </flux:button>
                    </div>
                </div>
            </flux:card>

            <!-- Import Results -->
            @if($showImportResults)
                <flux:card class="mt-6">
                    <flux:heading size="lg" class="mb-4">{{ __('messages.import_export.import_results') }}</flux:heading>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <flux:heading size="lg" class="text-green-600 dark:text-green-400">
                                    {{ $importResults['imported'] ?? 0 }}
                                </flux:heading>
                                <flux:text variant="subtle">{{ __('messages.import_export.imported') }}</flux:text>
                            </div>
                            
                            <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <flux:heading size="lg" class="text-yellow-600 dark:text-yellow-400">
                                    {{ $importResults['skipped'] ?? 0 }}
                                </flux:heading>
                                <flux:text variant="subtle">{{ __('messages.import_export.skipped') }}</flux:text>
                            </div>
                            
                            @if(!empty($importResults['errors']))
                                <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <flux:heading size="lg" class="text-red-600 dark:text-red-400">
                                        {{ count($importResults['errors']) }}
                                    </flux:heading>
                                    <flux:text variant="subtle">{{ __('messages.import_export.errors') }}</flux:text>
                                </div>
                            @endif
                        </div>

                        @if(!empty($importResults['errors']))
                            <flux:callout variant="danger" icon="exclamation-triangle">
                                <flux:callout.heading>{{ __('messages.import_export.import_errors') }}</flux:callout.heading>
                                <flux:callout.text>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($importResults['errors'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </flux:callout.text>
                            </flux:callout>
                        @endif
                    </div>
                </flux:card>
            @endif
        </flux:tab.panel>
    </flux:tab.group>
</div> 