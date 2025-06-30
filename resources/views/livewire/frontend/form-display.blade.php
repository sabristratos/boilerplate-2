<div class="form-display">
    @if($submitted)
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>Form Submitted!</flux:callout.heading>
            <flux:callout.text>
                {{ $successMessage }}
            </flux:callout.text>
        </flux:callout>
    @else
        <form wire:submit="submit" class="space-y-6">
            <flux:heading size="lg">{{ $form->getTranslation('name', app()->getLocale()) }}</flux:heading>
            
            @if($form->elements)
                <div class="grid grid-cols-12 gap-4">
                    @foreach($form->elements as $index => $element)
                        @php
                            $fieldName = 'field_' . $element['id'];
                            $width = $element['styles']['desktop']['width'] ?? 'full';
                            
                            // Convert width to column span
                            $columnSpan = match($width) {
                                'full' => 12,
                                '1/2' => 6,
                                '1/3' => 4,
                                '2/3' => 8,
                                '1/4' => 3,
                                '3/4' => 9,
                                default => 12
                            };
                        @endphp
                        
                        <div class="col-span-{{ $columnSpan }}">
                            {!! $renderedElements[$index] ?? '' !!}
                        </div>
                    @endforeach
                </div>
            @else
                <flux:callout variant="secondary" icon="information-circle">
                    <flux:callout.text>
                        This form has no elements configured yet.
                    </flux:callout.text>
                </flux:callout>
            @endif
            
            <div class="flex justify-end">
                <flux:button type="submit" icon="paper-airplane">
                    Submit Form
                </flux:button>
            </div>
        </form>
    @endif
</div> 