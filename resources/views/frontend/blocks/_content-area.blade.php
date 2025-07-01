@props([
    'block',
    'data' => [],
    'alpine' => false,
    'preview' => false,
])

@php
    $blockData = $block ? $block->getTranslatedData() : $data;
@endphp

<div x-data="{ data: {{ json_encode($blockData) }} }" class="w-full">
    <div class="prose dark:prose-invert max-w-none {{ $preview ? 'p-2' : 'p-4' }}">
        @if ($alpine)
            <div x-html="data.content"></div>
        @else
            {!! $blockData['content'] ?? '' !!}
        @endif
    </div>

    @if(($blockData['show_form'] ?? false) && ($blockData['form_id'] ?? false))
        @if(!$preview)
            @php
                $formPosition = $blockData['form_position'] ?? 'bottom';
                $formClasses = match($formPosition) {
                    'top' => 'mb-4',
                    'bottom' => 'mt-4',
                    'inline' => 'my-4',
                    default => 'mt-4'
                };
            @endphp
            
            @if($formPosition === 'top')
                <div class="{{ $formClasses }}">
                    @livewire('frontend.form-display', ['formId' => $blockData['form_id']])
                </div>
                <div class="prose dark:prose-invert max-w-none p-4">
                    @if ($alpine)
                        <div x-html="data.content"></div>
                    @else
                        {!! $blockData['content'] ?? '' !!}
                    @endif
                </div>
            @elseif($formPosition === 'inline')
                <div class="{{ $formClasses }} prose dark:prose-invert max-w-none">
                    @livewire('frontend.form-display', ['formId' => $blockData['form_id']])
                </div>
            @else
                <div class="{{ $formClasses }}">
                    @livewire('frontend.form-display', ['formId' => $blockData['form_id']])
                </div>
            @endif
        @else
            <div class="mt-2 text-xs text-zinc-500 bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded">
                Form: {{ $blockData['form_id'] }} ({{ $blockData['form_position'] ?? 'bottom' }})
            </div>
        @endif
    @endif
</div> 