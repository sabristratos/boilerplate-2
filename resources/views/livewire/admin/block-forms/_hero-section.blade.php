<div>
    <div class="space-y-4">
        <flux:input label="Heading" wire:model.live="state.heading" />
        <flux:textarea label="Subheading" wire:model.live="state.subheading" rows="3" />

        <div>
            <flux:label>Image</flux:label>
            <div class="mt-1">
                <livewire:media-uploader :model="$editingBlock" collection="image" />
            </div>
        </div>
    </div>
</div>
