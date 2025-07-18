@props(['form', 'elements', 'selectedElementId', 'activeBreakpoint', 'isPreviewMode', 'renderedElements'])

<div 
    class="flex-1 p-4 overflow-y-auto form-canvas bg-zinc-50 dark:bg-zinc-900/50" 
    @drop.prevent="
        const type = $event.dataTransfer.getData('type');
        if (type) {
            $wire.addElement(type);
        }
    " 
    @dragover.prevent
    @dragover="$el.classList.add('drag-over')"
    @dragleave="$el.classList.remove('drag-over')"
    @drop="$el.classList.remove('drag-over')"
>
    @if($isPreviewMode)
        <!-- Preview Mode -->
        <div
            class="mx-auto border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 transition-all duration-300"
            :style="{ backgroundColor: $wire.settings.backgroundColor, fontFamily: $wire.settings.defaultFont }"
            :class="{
                'max-w-full': $wire.activeBreakpoint === 'desktop',
                'max-w-3xl': $wire.activeBreakpoint === 'tablet',
                'max-w-sm': $wire.activeBreakpoint === 'mobile',
            }"
        >
            <form wire:submit.prevent="submitPreview" class="p-4 space-y-4">
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
                            <div class="responsive-grid-item" style="grid-column: span {{ $columnSpan }};">
                                <div data-preview-element-id="{{ $element['id'] }}">
                                    {!! $renderedElements[$index] ?? '<div class="text-red-500">'.__('forms.builder.element_not_rendered').'</div>' !!}
                                </div>
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
                
                <div class="flex justify-end pt-2">
                    <flux:button type="submit" icon="paper-airplane">
                        {{ __('messages.forms.form_builder_interface.submit_form') }}
                    </flux:button>
                </div>
            </form>
        </div>
    @else
        <!-- Builder Mode -->
        <div
            class="mx-auto border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 transition-all duration-300"
            :style="{ backgroundColor: $wire.settings.backgroundColor, fontFamily: $wire.settings.defaultFont }"
            :class="{
                'max-w-full': $wire.activeBreakpoint === 'desktop',
                'max-w-3xl': $wire.activeBreakpoint === 'tablet',
                'max-w-sm': $wire.activeBreakpoint === 'mobile',
            }"
        >
            <div
                class="responsive-grid-container min-h-[300px] p-4"
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
                        x-sort:item="{{ $element['order'] ?? $index }}"
                        @click="$wire.selectElement('{{ $element['id'] }}')"
                        class="relative cursor-pointer group [body:not(.sorting)_&]:hover:bg-zinc-50 dark:[body:not(.sorting)_&]:hover:bg-zinc-800/50 rounded-md responsive-grid-item"
                        :class="{ 'ring-2 ring-primary-500 ring-offset-2 ring-offset-zinc-100 dark:ring-offset-zinc-900': $wire.selectedElementId === '{{ $element['id'] }}' }"
                        style="grid-column: span {{ $columnSpan }};"
                    >
                        <div class="absolute top-1 right-1 z-10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                            <flux:tooltip content="{{ __('messages.forms.form_builder_interface.drag_to_reorder') }}">
                                <flux:button 
                                    icon="arrows-pointing-out" 
                                    size="xs" 
                                    variant="ghost" 
                                    x-sort:handle 
                                />
                            </flux:tooltip>
                            <flux:tooltip content="{{ __('forms.buttons.duplicate') }}">
                                <flux:button 
                                    wire:click="duplicateElement('{{ $element['id'] }}')" 
                                    icon="document-duplicate" 
                                    size="xs" 
                                    variant="ghost" 
                                />
                            </flux:tooltip>
                            <flux:tooltip content="{{ __('messages.forms.form_builder_interface.delete_element') }}">
                                <flux:button 
                                    wire:click="confirmDelete('{{ $element['id'] }}', 'deleteElement')" 
                                    icon="trash" 
                                    size="xs" 
                                    variant="danger" 
                                />
                            </flux:tooltip>
                        </div>
                        <div class="p-2" data-edit-element-id="{{ $element['id'] }}">
                            {!! $renderedElements[$index] ?? '<div class="text-red-500">'.__('forms.builder.element_not_rendered').'</div>' !!}
                        </div>
                    </div>
                @empty
                    <div class="text-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 col-span-full p-12 rounded-lg bg-zinc-50 dark:bg-zinc-800/30 transition-all duration-300 hover:border-primary-400 dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-950/20">
                        <div class="max-w-md mx-auto">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                <flux:icon name="plus-circle" class="size-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <flux:heading size="lg" class="mb-2">{{ __('forms.builder.start_building_form') }}</flux:heading>
                            <flux:text variant="subtle" class="mb-4">
                                {{ __('forms.builder.drag_drop_elements') }}
                            </flux:text>
                            <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="flex items-center justify-center space-x-2">
                                    <flux:icon name="arrow-left" class="size-4" />
                                    <span>{{ __('forms.builder.select_elements_toolbox') }}</span>
                                </div>
                                <div class="flex items-center justify-center space-x-2">
                                    <flux:icon name="arrow-down" class="size-4" />
                                    <span>{{ __('forms.builder.drag_here_add_form') }}</span>
                                </div>
                                <div class="flex items-center justify-center space-x-2">
                                    <flux:icon name="arrows-pointing-out" class="size-4" />
                                    <span>{{ __('forms.builder.reorder_elements_dragging') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div> 
