@props(['block', 'alpine' => false])

<div class="px-4 py-8">
    @if ($alpine)
        <template x-for="(faq, index) in state.faqs" :key="index">
            <details class="mb-2">
                <summary class="font-semibold" x-text="faq.question"></summary>
                <p class="mt-1" x-text="faq.answer"></p>
            </details>
        </template>
    @else
        @foreach ($block->data['faqs'] ?? [] as $faq)
            <details class="mb-2">
                <summary class="font-semibold">{{ $block->getTranslation('data', app()->getLocale())['faqs'][$loop->index]['question'] ?? $faq['question'] }}</summary>
                <p class="mt-1">{{ $block->getTranslation('data', app()->getLocale())['faqs'][$loop->index]['answer'] ?? $faq['answer'] }}</p>
            </details>
        @endforeach
    @endif
</div> 