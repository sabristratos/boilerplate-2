<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="list-bullet" class="size-4" />
        Options
    </flux:heading>
    
    <div class="space-y-3">
        @foreach($options as $index => $option)
            <div wire:key="option-{{ $index }}" class="flex items-start space-x-2 p-3 border border-zinc-200 dark:border-zinc-700 rounded-md">
                <div class="flex-grow grid gap-4" style="grid-template-columns: 1fr 1fr;">
                    <flux:input
                        wire:model.blur="options.{{ $index }}.value"
                        label="Value"
                        placeholder="e.g. admin"
                        help="The value that will be submitted"
                    />
                    <flux:input
                        wire:model.blur="options.{{ $index }}.label"
                        label="Label"
                        placeholder="e.g. Administrator"
                        help="The text displayed to users"
                    />
                </div>
                <div class="pt-6">
                    <flux:button 
                        wire:click="removeOption({{ $index }})" 
                        variant="danger" 
                        size="sm" 
                        icon="trash" 
                        tooltip="Remove option" 
                    />
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        <flux:button 
            wire:click="addOption" 
            variant="subtle" 
            icon="plus"
        >
            Add Option
        </flux:button>
    </div>
</div> 