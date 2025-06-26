@props(['alpine' => false])
<div>
    <flux:select
        label="Select a Global Block"
        wire:model.defer="state.global_block_id"
        placeholder="Choose a block to display..."
        description="Link to a block that you've created in the Global Blocks section."
    >
        @foreach(\App\Models\GlobalBlock::all() as $globalBlock)
            <flux:select.option value="{{ $globalBlock->id }}">
                {{ $globalBlock->name }} ({{ Str::title(str_replace('-', ' ', $globalBlock->type)) }})
            </flux:select.option>
        @endforeach
    </flux:select>
</div> 