@props(['block', 'data' => [], 'alpine' => false, 'preview' => false])

@php
    $blockData = $block ? $block->getTranslatedData() : $data;
    $style = $blockData['style'] ?? 'accordion';
    $expandFirst = $blockData['expand_first'] ?? false;
@endphp

<div class="w-full {{ $preview ? 'p-2' : 'p-4' }}">
    @if($blockData['heading'] ?? false)
        <div class="text-center mb-6">
            <flux:heading size="lg" class="mb-2">{{ $blockData['heading'] }}</flux:heading>
            @if($blockData['subheading'] ?? false)
                <flux:text variant="subtle">{{ $blockData['subheading'] }}</flux:text>
            @endif
        </div>
    @endif

    @if($preview)
        <div class="text-xs text-zinc-500 space-y-2">
            <div class="font-semibold">Sample FAQ Items ({{ $style }})</div>
            <div class="space-y-1">
                <div class="font-medium">Sample Question 1</div>
                <div class="text-xs">This is a preview of how FAQ items will appear...</div>
            </div>
            <div class="space-y-1">
                <div class="font-medium">Sample Question 2</div>
                <div class="text-xs">Another preview item...</div>
            </div>
        </div>
    @else
        @if($style === 'accordion')
            <flux:accordion :exclusive="!$expandFirst">
                @foreach (($blockData['faqs'] ?? []) as $index => $faq)
                    <flux:accordion.item 
                        :expanded="$expandFirst && $index === 0"
                        heading="{{ $faq['question'] ?? '' }}"
                    >
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $faq['answer'] ?? '' !!}
                        </div>
                    </flux:accordion.item>
                @endforeach
            </flux:accordion>
        @else
            <div class="space-y-4">
                @foreach (($blockData['faqs'] ?? []) as $faq)
                    <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4 last:border-b-0">
                        <flux:heading size="sm" class="mb-2">{{ $faq['question'] ?? '' }}</flux:heading>
                        <div class="prose dark:prose-invert max-w-none text-sm">
                            {!! $faq['answer'] ?? '' !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div> 