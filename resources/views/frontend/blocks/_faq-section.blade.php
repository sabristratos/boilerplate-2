@props(['block', 'data' => [], 'alpine' => false, 'preview' => false])

@php
    $blockData = $data ?: ($block ? $block->getTranslatedData() : []);
    $style = $blockData['style'] ?? 'accordion';
    $expandFirst = $blockData['expand_first'] ?? false;
    $textAlignment = $blockData['text_alignment'] ?? 'center';
    $maxWidth = $blockData['max_width'] ?? 'max-w-4xl';
    $showIcons = $blockData['show_icons'] ?? true;
    $backgroundColor = $blockData['background_color'] ?? 'bg-white';
    
    // Map background colors to section backgrounds
    $sectionBackground = match($backgroundColor) {
        'bg-zinc-50' => 'bg-zinc-50 dark:bg-zinc-800',
        'bg-blue-50' => 'bg-blue-50 dark:bg-blue-900/20',
        'bg-gray-50' => 'bg-gray-50 dark:bg-gray-800',
        'transparent' => 'bg-transparent',
        default => 'bg-white dark:bg-zinc-900'
    };
@endphp

<section class="py-16 {{ $sectionBackground }}">
    <div class="{{ $maxWidth }} mx-auto px-4 sm:px-6 lg:px-8">
        @if($blockData['heading'] ?? false)
            <div class="text-{{ $textAlignment }} mb-12">
                <x-frontend.heading as="h2" class="text-3xl md:text-4xl font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $blockData['heading'] }}
                </x-frontend.heading>
                @if($blockData['subheading'] ?? false)
                    <p class="text-lg text-zinc-600 dark:text-zinc-300 max-w-3xl {{ $textAlignment === 'center' ? 'mx-auto' : '' }}">
                        {{ $blockData['subheading'] }}
                    </p>
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
                <div class="space-y-6">
                    @foreach (($blockData['faqs'] ?? []) as $faq)
                        <div class="border-b border-zinc-200 dark:border-zinc-700 pb-6 last:border-b-0">
                            <div class="flex items-start gap-3">
                                @if($showIcons)
                                    <div class="flex-shrink-0 mt-1">
                                        <flux:icon name="question-mark-circle" class="w-5 h-5 text-blue-500" />
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">
                                        {{ $faq['question'] ?? '' }}
                                    </h3>
                                    <div class="prose dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300">
                                        {!! $faq['answer'] ?? '' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section> 