<div class="relative z-50">
    <flux:modal wire:model.live="show" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $title }}</flux:heading>
                <flux:text class="mt-2">{!! $message !!}</flux:text>
            </div>

            <div class="flex items-center gap-2">
                <flux:spacer />
                <flux:button :variant="$cancelVariant" wire:click="$set('show', false)">{{ $cancelText }}</flux:button>
                <flux:button :variant="$confirmVariant" wire:click="confirm">{{ $confirmText }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div> 