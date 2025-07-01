@props(['form', 'elements', 'selectedElementId', 'activeBreakpoint', 'isPreviewMode', 'previewElements', 'renderedElements'])
<div 
    class="flex-1 p-8 overflow-y-auto" 
    @drop.prevent="handleDrop($event)" 
    @dragover.prevent
    x-data="formCanvas()"
    @element-updated.window="refreshPreview()"
    @preview-element-updated.window="updatePreviewElement($event)"
    @edit-element-updated.window="updateEditElement($event)"
>
    @if($isPreviewMode)
        <!-- Preview Mode -->
        <div
            class="mx-auto shadow-lg transition-all duration-300"
            :style="{ backgroundColor: $wire.settings.backgroundColor, fontFamily: $wire.settings.defaultFont }"
            :class="{
                'max-w-full': $wire.activeBreakpoint === 'desktop',
                'max-w-3xl': $wire.activeBreakpoint === 'tablet',
                'max-w-sm': $wire.activeBreakpoint === 'mobile',
            }"
        >
            <form wire:submit.prevent="submitPreview" class="p-6 space-y-6">
                <flux:heading size="lg">{{ $form->getTranslation('name', 'en') }}</flux:heading>
                
                @if($elements)
                    <div class="responsive-grid-container">
                        @foreach($elements as $index => $element)
                            @php
                                $activeWidth = $element['styles'][$activeBreakpoint]['width'] ?? 'full';
                                $columnSpan = match($activeWidth) {
                                    'full' => 12,
                                    '1/2' => 6,
                                    '1/3' => 4,
                                    '2/3' => 8,
                                    '1/4' => 3,
                                    '3/4' => 9,
                                    default => 12
                                };
                                if ($activeBreakpoint === 'mobile') {
                                    $columnSpan = 12;
                                }
                            @endphp
                            <div class="responsive-grid-item" style="grid-column: span {{ $columnSpan }};" wire:key="preview-element-{{ $element['id'] }}" data-preview-element="{{ $element['id'] }}">
                                {!! $previewElements[$index] ?? '' !!}
                            </div>
                        @endforeach
                    </div>
                @else
                    <flux:callout variant="secondary" icon="information-circle">
                        <flux:callout.text>
                            {{ __('messages.forms.form_builder_interface.form_has_no_elements') }}
                        </flux:callout.text>
                    </flux:callout>
                @endif
                
                <div class="flex justify-end">
                    <flux:button type="submit" icon="paper-airplane">
                        {{ __('messages.forms.form_builder_interface.submit_form') }}
                    </flux:button>
                </div>
            </form>
        </div>
    @else
        <!-- Builder Mode -->
        <div
            class="mx-auto shadow-lg transition-all duration-300"
            :style="{ backgroundColor: $wire.settings.backgroundColor, fontFamily: $wire.settings.defaultFont }"
            :class="{
                'max-w-full': $wire.activeBreakpoint === 'desktop',
                'max-w-3xl': $wire.activeBreakpoint === 'tablet',
                'max-w-sm': $wire.activeBreakpoint === 'mobile',
            }"
        >
            <div
                class="responsive-grid-container"
                x-sort="
                    const items = Array.from($el.children).map(child => parseInt(child.getAttribute('x-sort:item')));
                    $wire.handleReorder(items);
                "
                x-sort:config="{ animation: 150, ghostClass: 'sortable-ghost' }"
            >
                @forelse($elements as $index => $element)
                    @php
                        // Use the active breakpoint to determine which width to apply
                        $activeWidth = $element['styles'][$activeBreakpoint]['width'] ?? 'full';
                        
                        // Convert width to column span based on active breakpoint
                        $columnSpan = match($activeWidth) {
                            'full' => 12,
                            '1/2' => 6,
                            '1/3' => 4,
                            '2/3' => 8,
                            '1/4' => 3,
                            '3/4' => 9,
                            default => 12
                        };
                        
                        // Force full width on mobile for better UX
                        if ($activeBreakpoint === 'mobile') {
                            $columnSpan = 12;
                        }
                    @endphp
                    <div
                        wire:key="element-{{ $element['id'] }}"
                        x-sort:item="{{ $element['order'] }}"
                        @click="$wire.set('selectedElementId', '{{ $element['id'] }}')"
                        class="relative cursor-pointer group [body:not(.sorting)_&]:hover:bg-zinc-50 dark:[body:not(.sorting)_&]:hover:bg-zinc-800/50 rounded-md responsive-grid-item"
                        :class="{ 'ring-2 ring-blue-500 ring-offset-2 ring-offset-zinc-100 dark:ring-offset-zinc-900': $wire.selectedElementId === '{{ $element['id'] }}' }"
                        style="grid-column: span {{ $columnSpan }};"
                    >
                        <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                            <flux:button 
                                icon="arrows-pointing-out" 
                                size="xs" 
                                variant="ghost" 
                                x-sort:handle 
                                :tooltip="__('messages.forms.form_builder_interface.drag_to_reorder')"
                            />
                            <flux:button 
                                wire:click="confirmDelete('{{ $element['id'] }}', 'deleteElement')" 
                                icon="trash" 
                                size="xs" 
                                variant="danger" 
                                :tooltip="__('messages.forms.form_builder_interface.delete_element')"
                            />
                        </div>
                        <div class="p-4">
                            {!! $renderedElements[$index] ?? '' !!}
                        </div>
                    </div>
                @empty
                    <div class="text-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 col-span-full p-12 rounded-lg">
                        <flux:heading>{{ __('messages.forms.form_builder_interface.drop_elements_here') }}</flux:heading>
                        <flux:text variant="subtle">{{ __('messages.forms.form_builder_interface.drag_drop_instructions') }}</flux:text>
                        <flux:callout variant="secondary" icon="information-circle" class="mt-4 max-w-md mx-auto">
                            <flux:callout.text>
                                <p>{{ __('messages.forms.form_builder_interface.drag_form_elements') }}</p>
                                <p>{{ __('messages.forms.form_builder_interface.reorder_instructions') }}</p>
                            </flux:callout.text>
                        </flux:callout>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div> 
