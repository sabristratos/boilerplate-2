@props([
    'block',
    'data' => [],
    'preview' => false,
])

@php
    $blockData = $data ?: ($block ? array_merge($block->getTranslatedData(), $block->getSettingsArray()) : []);
    $backgroundColors = [
        'white' => 'bg-white dark:bg-zinc-900',
        'gray' => 'bg-zinc-50 dark:bg-zinc-800',
        'primary' => 'bg-blue-50 dark:bg-blue-900/20',
        'secondary' => 'bg-zinc-100 dark:bg-zinc-800',
    ];
    $textAlignments = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
    $backgroundClass = $backgroundColors[$blockData['background_color'] ?? 'white'] ?? $backgroundColors['white'];
    $textAlignmentClass = $textAlignments[$blockData['text_alignment'] ?? 'center'] ?? $textAlignments['center'];
@endphp

<section class="py-16 {{ $backgroundClass }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="{{ $textAlignmentClass }} mb-12">
                @if($blockData['heading'] ?? false)
                    <x-frontend.heading as="h2" class="text-3xl md:text-4xl font-bold text-zinc-900 dark:text-white mb-4">
                        {{ $blockData['heading'] }}
                    </x-frontend.heading>
                @endif

                @if($blockData['subheading'] ?? false)
                    <p class="text-lg text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto">
                        {{ $blockData['subheading'] }}
                    </p>
                @endif
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                <!-- Contact Form -->
                <div class="order-2 lg:order-1">
                    @if($blockData['form_id'] ?? false)
                        @php
                            $form = \App\Models\Form::find($blockData['form_id']);
                        @endphp
                        @if($form)
                            <div>
                                <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-6">
                                    {{ $form->getTranslation('name', app()->getLocale()) }}
                                </h3>
                                
                                @livewire('frontend.form-display', ['form' => $form->id], key('form-' . $form->id))
                            </div>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <p class="text-yellow-800 dark:text-yellow-200">
                                    {{ __('blocks.contact.form_not_found') }}
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="bg-zinc-50 dark:bg-zinc-800 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-8 text-center">
                            <div class="text-zinc-400 dark:text-zinc-500 mb-4">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ __('blocks.contact.no_form_selected') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Contact Information -->
                @if($blockData['show_contact_info'] ?? false)
                    <div class="order-1 lg:order-2">
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-6">
                                {{ __('blocks.contact.contact_info_title') }}
                            </h3>

                            @if($blockData['contact_info']['email'] ?? false)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                            {{ __('blocks.contact.email_label') }}
                                        </p>
                                        <a href="mailto:{{ $blockData['contact_info']['email'] }}" class="text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $blockData['contact_info']['email'] }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($blockData['contact_info']['phone'] ?? false)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                            {{ __('blocks.contact.phone_label') }}
                                        </p>
                                        <a href="tel:{{ $blockData['contact_info']['phone'] }}" class="text-zinc-900 dark:text-white hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                            {{ $blockData['contact_info']['phone'] }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($blockData['contact_info']['address'] ?? false)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                            {{ __('blocks.contact.address_label') }}
                                        </p>
                                        <p class="text-zinc-900 dark:text-white">
                                            {{ $blockData['contact_info']['address'] }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section> 