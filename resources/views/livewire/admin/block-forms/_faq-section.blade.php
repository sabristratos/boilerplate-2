@props(['alpine' => false])

<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white">FAQ Items</h3>

    <div class="mt-4 space-y-4">
        <template x-for="(faq, index) in state.faqs" :key="index">
            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg space-y-2 relative">
                <div class="absolute top-2 right-2">
                    <flux:button @click="state.faqs.splice(index, 1)" size="xs" variant="danger" icon="trash" />
                </div>
                <flux:input x-model="faq.question" label="Question" />
                <flux:textarea x-model="faq.answer" label="Answer" />
            </div>
        </template>
    </div>

    <div class="mt-4">
        <flux:button @click="state.faqs.push({question: '', answer: ''})" variant="outline" icon="plus">
            Add FAQ Item
        </flux:button>
    </div>
</div>
