@props(['block', 'alpine' => false])

<div
    class="bg-zinc-100 dark:bg-zinc-800 p-8 rounded-lg"
    :class="{ 'ring-2 ring-blue-500': editingBlockId == {{ $block->id }} }"
>
    @php
        $heading = $block->data['heading'] ?? '';
        $subheading = $block->data['subheading'] ?? '';
    @endphp

    <h1
        class="text-4xl font-bold text-center"
        x-text="editingBlockId == {{ $block->id }} && liveState ? liveState.heading : @json($heading)"
    ></h1>
    <p
        class="text-lg text-center mt-2"
        x-text="editingBlockId == {{ $block->id }} && liveState ? liveState.subheading : @json($subheading)"
    ></p>

    @if($block->hasMedia('images'))
        <div class="mt-4">
            {{ $block->getFirstMedia('images') }}
        </div>
    @endif
</div> 