<div class="form-display">
    @if(isset($error))
        <flux:callout variant="danger" icon="exclamation-triangle">
            <flux:callout.text>
                {{ $error }}
            </flux:callout.text>
        </flux:callout>
    @elseif($submitted)
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>Form Submitted!</flux:callout.heading>
            <flux:callout.text>
                {{ $successMessage }}
            </flux:callout.text>
        </flux:callout>
    @else
        <form wire:submit="submit" class="space-y-6">
            @if($form && $form->elements)
                <div class="grid grid-cols-12 gap-6">
                    @foreach($form->elements as $index => $element)
                        @php
                            $fieldName = 'field_' . $element['id'];
                            
                            // Get width from styles, with fallback to default
                            $width = 'full';
                            if (isset($element['styles']['desktop']['width'])) {
                                $width = $element['styles']['desktop']['width'];
                            } elseif (isset($element['styles']['width'])) {
                                $width = $element['styles']['width'];
                            }
                            
                            // Convert width to column span with responsive classes
                            $columnSpan = match($width) {
                                'full' => 'col-span-12',
                                '1/2' => 'col-span-12 md:col-span-6',
                                '1/3' => 'col-span-12 md:col-span-4',
                                '2/3' => 'col-span-12 md:col-span-8',
                                '1/4' => 'col-span-12 md:col-span-3',
                                '3/4' => 'col-span-12 md:col-span-9',
                                default => 'col-span-12'
                            };
                        @endphp
                        
                        <div class="{{ $columnSpan }}">
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