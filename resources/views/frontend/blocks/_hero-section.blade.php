@props([
    'block',
    'data' => [],
    'alpine' => false
])

<div x-data="{ data: {{ json_encode($data) }} }">
    <div class="container mx-auto px-4 py-8 rounded-lg">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="text-center lg:text-left">
                @php
                    $overline = $data['overline'] ?? '';
                    $heading = $data['heading'] ?? '';
                    $subheading = $data['subheading'] ?? '';
                    $buttons = $data['buttons'] ?? [];
                @endphp

                @if($alpine)
                    <p class="text-sm font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400" x-text="data.overline"></p>
                    <h1 class="mt-2 text-4xl font-bold" x-text="data.heading"></h1>
                    <p class="mt-4 text-lg" x-text="data.subheading"></p>
                @else
                    @if($overline)
                        <p class="text-sm font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">{{ $overline }}</p>
                    @endif
                    <h1 class="mt-2 text-4xl font-bold">{{ $heading }}</h1>
                    @if($subheading)
                        <p class="mt-4 text-lg">{{ $subheading }}</p>
                    @endif
                @endif

                @if($alpine)
                    <div class="mt-6 flex items-center justify-center lg:justify-start gap-2" x-show="data.buttons && data.buttons.length > 0">
                        <template x-for="(button, index) in data.buttons" :key="index">
                            <a :href="button.url" class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition"
                               :class="{
                                   'bg-blue-600 text-white border-transparent hover:bg-blue-500': button.variant === 'primary',
                                   'bg-white text-zinc-700 border-zinc-300 hover:bg-zinc-50': button.variant === 'secondary',
                                   'text-zinc-600 hover:bg-zinc-100': button.variant === 'ghost'
                               }"
                               x-text="button.text"></a>
                        </template>
                    </div>
                @else
                    @if(count($buttons) > 0)
                        <div class="mt-6 flex items-center justify-center lg:justify-start gap-2">
                            @foreach($buttons as $button)
                                <a href="{{ $button['url'] }}" class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition
                                    @if($button['variant'] === 'primary') bg-blue-600 text-white border-transparent hover:bg-blue-500 @endif
                                    @if($button['variant'] === 'secondary') bg-white text-zinc-700 border-zinc-300 hover:bg-zinc-50 @endif
                                    @if($button['variant'] === 'ghost') text-zinc-600 hover:bg-zinc-100 @endif
                                ">
                                    {{ $button['text'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            @if($block->hasMedia('image'))
                <div class="mt-4 lg:mt-0 w-full">
                    <img src="{{ $block->getFirstMedia('image')->getUrl() }}" alt="{{ $data['heading'] ?? 'Hero image' }}" class="w-full h-auto object-cover rounded-lg">
                </div>
            @endif
        </div>
    </div>
</div>
