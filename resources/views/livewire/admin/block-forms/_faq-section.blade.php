<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white">FAQ Items</h3>
    <div class="mt-4 space-y-4">
        @foreach ($state['faqs'] ?? [] as $index => $faq)
            <div key="faq-{{ $index }}" class="p-4 border border-gray-200 rounded-md dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h4 class="font-medium text-gray-900 dark:text-white">FAQ {{ $index + 1 }}</h4>
                    <flux:button type="button" variant="subtle" wire:click="removeFaqItem({{ $index }})">
                        Remove
                    </flux:button>
                </div>
                <div class="mt-4 space-y-4">
                    <flux:input label="Question" wire:model.live="state.faqs.{{ $index }}.question" />
                    <flux:textarea label="Answer" wire:model.live="state.faqs.{{ $index }}.answer" rows="3" />
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        <flux:button type="button" wire:click="addFaqItem">
            Add FAQ Item
        </flux:button>
    </div>
</div>
