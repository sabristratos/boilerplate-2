<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <flux:button href="{{ route('media.index') }}" variant="ghost" icon="arrow-left" size="sm">
                        {{ __('media.back_to_library') }}
                    </flux:button>
                    <flux:heading>{{ __('media.details') }}</flux:heading>
                </div>
                <div class="flex items-center space-x-2">
                    <flux:button
                        wire:click="deleteMedia"
                        :wire:confirm="__('media.delete_confirm')"
                        variant="danger"
                        icon="trash"
                    >
                        {{ __('media.delete') }}
                    </flux:button>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Media Preview -->
                <div class="md:col-span-1">
                    <flux:card>
                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            @if(str_contains($media->mime_type, 'image'))
                                <img
                                    src="{{ $media->getUrl() }}"
                                    alt="{{ $media->name }}"
                                    class="h-full w-full object-cover object-center"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <flux:icon name="{{ $this->getIconForMimeType($media->mime_type) }}" class="h-24 w-24 text-gray-400" />
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 flex justify-center">
                            <flux:button href="{{ $media->getUrl() }}" target="_blank" variant="outline" icon="arrow-down-tray">
                                {{ __('media.download') }}
                            </flux:button>
                        </div>
                    </flux:card>
                </div>

                <!-- Media Metadata -->
                <div class="md:col-span-2">
                    <flux:card>
                        <flux:heading size="lg">{{ __('media.metadata') }}</flux:heading>

                        <div class="mt-4 space-y-4">
                            <flux:field>
                                <flux:label>{{ __('media.sort_by_name') }}</flux:label>
                                <flux:input value="{{ $media->name }}" readonly />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('media.file_name') }}</flux:label>
                                <flux:input value="{{ $media->file_name }}" readonly />
                            </flux:field>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>{{ __('media.mime_type') }}</flux:label>
                                    <flux:input value="{{ $media->mime_type }}" readonly />
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('media.sort_by_size') }}</flux:label>
                                    <flux:input value="{{ $this->formatFileSize($media->size) }}" readonly />
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>{{ __('media.created_at') }}</flux:label>
                                    <flux:input value="{{ $media->created_at->format('Y-m-d H:i:s') }}" readonly />
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('media.updated_at') }}</flux:label>
                                    <flux:input value="{{ $media->updated_at->format('Y-m-d H:i:s') }}" readonly />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>{{ __('media.collection') }}</flux:label>
                                <flux:input value="{{ $media->collection_name }}" readonly />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('media.disk') }}</flux:label>
                                <flux:input value="{{ $media->disk }}" readonly />
                            </flux:field>

                            @if($media->model_type && $media->model_id)
                                <flux:field>
                                    <flux:label>{{ __('media.associated_with') }}</flux:label>
                                    <flux:input value="{{ class_basename($media->model_type) }} (ID: {{ $media->model_id }})" readonly />
                                </flux:field>
                            @endif

                            @if(!empty($media->custom_properties))
                                <div class="mt-6">
                                    <flux:heading size="md">{{ __('media.custom_properties') }}</flux:heading>
                                    <div class="mt-2">
                                        <flux:accordion>
                                            <flux:accordion.item :heading="__('media.view_custom_properties')">
                                                <pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded-md overflow-auto text-sm">{{ json_encode($media->custom_properties, JSON_PRETTY_PRINT) }}</pre>
                                            </flux:accordion.item>
                                        </flux:accordion>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($media->responsive_images))
                                <div class="mt-6">
                                    <flux:heading size="md">{{ __('media.responsive_images') }}</flux:heading>
                                    <div class="mt-2">
                                        <flux:accordion>
                                            <flux:accordion.item :heading="__('media.view_responsive_images')">
                                                <pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded-md overflow-auto text-sm">{{ json_encode($media->responsive_images, JSON_PRETTY_PRINT) }}</pre>
                                            </flux:accordion.item>
                                        </flux:accordion>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>
    </div>
</div>
