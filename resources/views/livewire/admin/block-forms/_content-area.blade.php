@props(['alpine' => false])

<div>
    @if ($alpine)
        <flux:textarea x-model="state.content" label="Content" rows="8" />
    @else
        <flux:textarea wire:model.defer="state.content" label="Content" rows="8" />
    @endif
</div>
