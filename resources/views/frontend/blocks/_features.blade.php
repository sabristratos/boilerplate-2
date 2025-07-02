@props([
    'block',
    'data' => [],
    'preview' => false,
])

@php
    $blockData = $data ?: ($block ? array_merge($block->getTranslatedData(), $block->getSettingsArray()) : []);
    $layouts = [
        'grid' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8',
        'list' => 'space-y-8',
    ];
    $columns = $blockData['columns'] ?? 3;
    $layout = $blockData['layout'] ?? 'grid';
    
    // Adjust grid columns based on the columns setting
    if ($layout === 'grid') {
        $gridCols = [
            1 => 'grid-cols-1',
            2 => 'grid-cols-1 md:grid-cols-2',
            3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
            4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
        ];
        $layoutClass = 'grid ' . ($gridCols[$columns] ?? $gridCols[3]) . ' gap-8';
    } else {
        $layoutClass = $layouts[$layout] ?? $layouts['grid'];
    }
    
    $colors = [
        'blue' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30',
        'green' => 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30',
        'yellow' => 'text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30',
        'red' => 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30',
        'purple' => 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30',
        'indigo' => 'text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/30',
        'pink' => 'text-pink-600 dark:text-pink-400 bg-pink-100 dark:bg-pink-900/30',
        'orange' => 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30',
    ];
@endphp

<section class="py-16 bg-white dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            @if($blockData['heading'] ?? false)
                <x-frontend.heading as="h2" class="text-3xl md:text-4xl font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $blockData['heading'] }}
                </x-frontend.heading>
            @endif

            @if($blockData['subheading'] ?? false)
                <p class="text-lg text-zinc-600 dark:text-zinc-300 max-w-3xl mx-auto">
                    {{ $blockData['subheading'] }}
                </p>
            @endif
        </div>

        <!-- Features -->
        @if($layout === 'grid')
            <div class="grid {{ $layoutClass }}">
                @foreach($blockData['features'] ?? [] as $feature)
                    <div class="text-center group">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl {{ $colors[$feature['color'] ?? 'blue'] }} mb-6 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @switch($feature['icon'] ?? 'star')
                                    @case('shield-check')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        @break
                                    @case('bolt')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        @break
                                    @case('heart')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        @break
                                    @case('star')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        @break
                                    @default
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                @endswitch
                            </svg>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-3">
                            {{ $feature['title'] }}
                        </h3>
                        
                        <p class="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ $feature['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="max-w-4xl mx-auto space-y-8">
                @foreach($blockData['features'] ?? [] as $feature)
                    <div class="flex items-start space-x-6 group">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg {{ $colors[$feature['color'] ?? 'blue'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @switch($feature['icon'] ?? 'star')
                                    @case('shield-check')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        @break
                                    @case('bolt')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        @break
                                    @case('heart')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        @break
                                    @case('star')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        @break
                                    @default
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                @endswitch
                            </svg>
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">
                                {{ $feature['title'] }}
                            </h3>
                            
                            <p class="text-zinc-600 dark:text-zinc-300 leading-relaxed">
                                {{ $feature['description'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section> 