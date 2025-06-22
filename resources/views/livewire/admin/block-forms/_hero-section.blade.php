@props(['alpine' => false])

<div>
    <div class="space-y-4">
        @if($alpine)
            <div>
                <flux:input x-model="state.heading" label="Heading" />
            </div>
            <div>
                <flux:textarea x-model="state.subheading" label="Subheading" rows="3" />
            </div>
        @else
            <div>
                <flux:input wire:model.defer="state.heading" label="Heading" />
            </div>
            <div>
                <flux:textarea wire:model.defer="state.subheading" label="Subheading" rows="3" />
            </div>
        @endif

        <div>
            <flux:label>Image</flux:label>
            <div class="mt-1">
                <livewire:media-uploader :model="$editingBlock" collection="image" />
            </div>
        </div>
    </div>
</div>
