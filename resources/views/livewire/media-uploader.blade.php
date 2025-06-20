<div>
    <div class="mt-4">
        {{-- Current Media Preview --}}
        @if($mediaUrl)
            <div class="mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-24 h-24 overflow-hidden rounded-md border border-zinc-200 dark:border-zinc-700">
                        <img src="{{ $mediaUrl }}" alt="{{ $setting->label }}" class="w-full h-full object-cover">
                    </div>
                    <flux:button wire:click="remove" variant="danger" size="sm">
                        Remove
                    </flux:button>
                </div>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex items-center space-x-2">
            <flux:button x-on:click="$wire.showUploadModal = true" icon="arrow-up-tray">
                Upload
            </flux:button>
            <flux:button x-on:click="$wire.showUrlModal = true" icon="link">
                Add from URL
            </flux:button>
        </div>

        {{-- Upload Modal --}}
        <flux:modal wire:model.self="showUploadModal" class="md:w-96">
            <flux:heading>Upload Media</flux:heading>
            <div class="mt-4">
                <flux:input
                    type="file"
                    wire:model.live="file"
                    accept="image/*"
                />
                @error('file') <div class="mt-1 text-red-500 text-sm">{{ $message }}</div> @enderror

                {{-- File Preview --}}
                @if($file)
                    <div class="mt-4">
                        <div class="w-24 h-24 overflow-hidden rounded-md border border-zinc-200 dark:border-zinc-700">
                            <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif
            </div>
            <div class="pt-4 mt-4 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                <flux:button x-on:click="$wire.showUploadModal = false" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button wire:click="save" variant="primary">
                    Save
                </flux:button>
            </div>
        </flux:modal>

        {{-- URL Modal --}}
        <flux:modal wire:model.self="showUrlModal" class="md:w-96">
            <flux:heading>Add from URL</flux:heading>
            <div class="mt-4">
                <flux:input
                    wire:model.live="url"
                    placeholder="https://example.com/image.jpg"
                />
                @error('url') <div class="mt-1 text-red-500 text-sm">{{ $message }}</div> @enderror
            </div>
            <div class="pt-4 mt-4 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                <flux:button x-on:click="$wire.showUrlModal = false" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button wire:click="save" variant="primary">
                    Save
                </flux:button>
            </div>
        </flux:modal>
    </div>
</div>
