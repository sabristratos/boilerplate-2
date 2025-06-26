@props(['block', 'alpine' => false])

<div class="prose dark:prose-invert max-w-none p-4">
    @if ($alpine)
        <div x-html="state.content"></div>
    @else
        {!! $block->data['content'] ?? '' !!}
    @endif

    @if($block->data['form_id'] ?? false)
        <div class="mt-4">
            @livewire('frontend.form-display', ['formId' => $block->data['form_id']])
        </div>
    @endif
</div> 