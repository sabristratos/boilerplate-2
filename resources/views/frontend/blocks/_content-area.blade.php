@props(['block', 'alpine' => false])

<div class="prose dark:prose-invert max-w-none p-4">
    @if ($alpine)
        <div x-html="state.content"></div>
    @else
        {!! $block->data['content'] ?? '' !!}
    @endif
</div> 