@props([
    'block',
    'data' => [],
    'preview' => false,
])

@php
    use App\Models\Testimonial;
    $blockData = $data ?: ($block ? array_merge($block->getTranslatedData(), $block->getSettingsArray()) : []);
    $layouts = [
        'grid' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
        'carousel' => 'space-y-8',
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

    $showAvatars = $blockData['show_avatars'] ?? true;
    $showRatings = $blockData['show_ratings'] ?? true;
    $testimonials = Testimonial::orderBy('order')->get();
@endphp

<section class="py-16 bg-zinc-50 dark:bg-zinc-800">
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

        <!-- Testimonials -->
        @if($layout === 'grid')
            <div class="{{ $layoutClass }}">
                @foreach($testimonials as $testimonial)
                    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg p-6 md:p-8 border border-zinc-200 dark:border-zinc-700 hover:shadow-xl transition-shadow duration-300">
                        <!-- Quote -->
                        <div class="mb-6">
                            <svg class="w-8 h-8 text-blue-500 dark:text-blue-400 mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                            </svg>
                            
                            <p class="text-zinc-700 dark:text-zinc-300 text-lg leading-relaxed italic">
                                "{{ $testimonial->content }}"
                            </p>
                        </div>

                        <!-- Rating -->
                        @if($showRatings)
                            <div class="flex items-center mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= ($testimonial->rating ?? 5) ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        @endif

                        <!-- Author -->
                        <div class="flex items-center">
                            @if($showAvatars)
                                @if($testimonial->avatar)
                                    <img src="{{ $testimonial->avatar }}" alt="{{ $testimonial->name }}" class="w-12 h-12 rounded-full object-cover mr-4" />
                                @else
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-lg mr-4">
                                        {{ strtoupper(substr($testimonial->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                            @endif
                            
                            <div>
                                <h4 class="font-semibold text-zinc-900 dark:text-white">
                                    {{ $testimonial->name }}
                                </h4>
                                @if($testimonial->title)
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $testimonial->title }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Carousel layout would go here -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg p-8 md:p-12 border border-zinc-200 dark:border-zinc-700">
                    @if($testimonials->count())
                        @php $testimonial = $testimonials->first(); @endphp
                        
                        <div class="text-center">
                            <!-- Quote -->
                            <div class="mb-8">
                                <svg class="w-12 h-12 text-blue-500 dark:text-blue-400 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                </svg>
                                
                                <p class="text-zinc-700 dark:text-zinc-300 text-xl md:text-2xl leading-relaxed italic max-w-3xl mx-auto">
                                    "{{ $testimonial->content }}"
                                </p>
                            </div>

                            <!-- Rating -->
                            @if($showRatings)
                                <div class="flex items-center justify-center mb-6">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 {{ $i <= ($testimonial->rating ?? 5) ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            @endif

                            <!-- Author -->
                            <div class="flex items-center justify-center">
                                @if($showAvatars)
                                    @if($testimonial->avatar)
                                        <img src="{{ $testimonial->avatar }}" alt="{{ $testimonial->name }}" class="w-16 h-16 rounded-full object-cover mr-4" />
                                    @else
                                        <div class="flex-shrink-0 w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xl mr-4">
                                            {{ strtoupper(substr($testimonial->name ?? 'A', 0, 1)) }}
                                        </div>
                                    @endif
                                @endif
                                
                                <div>
                                    <h4 class="font-semibold text-zinc-900 dark:text-white text-lg">
                                        {{ $testimonial->name }}
                                    </h4>
                                    @if($testimonial->title)
                                        <p class="text-zinc-600 dark:text-zinc-400">
                                            {{ $testimonial->title }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section> 