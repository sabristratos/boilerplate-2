@props([
    'block',
    'data' => [],
    'alpine' => false
])

<div x-data="{ data: {{ json_encode($data) }} }">
    <div class="prose dark:prose-invert max-w-none p-4">
        @if ($alpine)
            <div x-html="data.content"></div>
        @else
            {!! $data['content'] ?? '' !!}
        @endif

        @if($data['form_id'] ?? false)
            <div class="mt-4">
                @livewire('frontend.form-display', ['formId' => $data['form_id']])
            </div>
        @endif
    </div>
</div> 