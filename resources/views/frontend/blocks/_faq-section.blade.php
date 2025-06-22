@props([
    'block'
])

@if($block->data['faqs'] ?? false)
    <section class="container px-4 py-12 mx-auto">
        <flux:accordion exclusive>
            @foreach($block->data['faqs'] as $faq)
                <flux:accordion.item heading="{{ $faq['question'] }}">
                    {{ $faq['answer'] }}
                </flux:accordion.item>
            @endforeach
        </flux:accordion>
    </section>
@endif 