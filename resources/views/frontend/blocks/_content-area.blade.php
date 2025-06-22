@props([
    'block'
])

@if($block->data['content'] ?? false)
    <section class="container px-4 py-12 mx-auto prose dark:prose-invert">
        {!! $block->data['content'] !!}
    </section>
@endif 