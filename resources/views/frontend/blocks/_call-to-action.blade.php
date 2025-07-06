@props([
    'block',
    'data' => [],
    'alpine' => false,
    'preview' => false,
])

@php
    $blockData = $data ?: ($block ? array_merge($block->getTranslatedData(), $block->getSettingsArray()) : []);
    $backgroundColors = [
        'white' => 'bg-white dark:bg-zinc-900',
        'gray' => 'bg-zinc-100 dark:bg-zinc-800',
        'primary' => 'bg-blue-600 dark:bg-blue-700',
        'secondary' => 'bg-zinc-600 dark:bg-zinc-700',
        // Legacy support for old color names
        'blue' => 'bg-blue-600 dark:bg-blue-700',
        'green' => 'bg-green-600 dark:bg-green-700',
        'purple' => 'bg-purple-600 dark:bg-purple-700',
        'orange' => 'bg-orange-600 dark:bg-orange-700',
        'red' => 'bg-red-600 dark:bg-red-700',
    ];
    $textAlignments = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
    $backgroundClass = $backgroundColors[$blockData['background_color'] ?? 'blue'] ?? $backgroundColors['blue'];
    $textAlignmentClass = $textAlignments[$blockData['text_alignment'] ?? 'center'] ?? $textAlignments['center'];
@endphp

<div x-data="{ 
    data: {{ json_encode($blockData) }},
    isEditing: {{ $alpine ? 'true' : 'false' }},
    getBackgroundClass() {
        const colors = {{ json_encode($backgroundColors) }};
        return colors[this.data.background_color || 'blue'] || colors['blue'];
    },
    getTextAlignmentClass() {
        const alignments = {{ json_encode($textAlignments) }};
        return alignments[this.data.text_alignment || 'center'] || alignments['center'];
    }
}" class="{{ $alpine ? '' : 'py-16 ' . $backgroundClass . ' relative overflow-hidden' }}" {{ $alpine ? 'x-bind:class="\'py-16 \' + getBackgroundClass() + \' relative overflow-hidden\'"' : '' }}>
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="{{ $alpine ? '' : $textAlignmentClass }}" {{ $alpine ? 'x-bind:class="getTextAlignmentClass()"' : '' }}>
                @if($blockData['heading'] ?? false)
                    <x-frontend.heading as="h2" class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
                        @if($alpine)
                            <span x-text="data.heading || ''"></span>
                        @else
                            {{ $blockData['heading'] }}
                        @endif
                    </x-frontend.heading>
                @endif

                @if($blockData['subheading'] ?? false)
                    <p class="text-xl text-blue-100 dark:text-blue-200 mb-8 max-w-3xl mx-auto">
                        @if($alpine)
                            <span x-text="data.subheading || ''"></span>
                        @else
                            {{ $blockData['subheading'] }}
                        @endif
                    </p>
                @endif

                @if(!empty($blockData['buttons'] ?? []))
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                        @if($alpine)
                            <template x-for="(button, index) in (data.buttons || [])" :key="index">
                                <x-frontend.button
                                    x-bind:href="button.url || '#'"
                                    x-bind:variant="button.variant || 'primary'"
                                    class="w-full sm:w-auto"
                                    x-text="button.text || ''"
                                />
                            </template>
                        @else
                            @foreach($blockData['buttons'] as $button)
                                <x-frontend.button
                                    :href="$button['url'] ?? '#'"
                                    :variant="$button['variant'] ?? 'primary'"
                                    class="w-full sm:w-auto"
                                >
                                    {{ $button['text'] }}
                                </x-frontend.button>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white/10 rounded-full transform -translate-y-1/2"></div>
    </div>
</div> 