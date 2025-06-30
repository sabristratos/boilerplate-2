<div
    class="flex h-screen bg-zinc-100 dark:bg-zinc-900 font-sans"
    x-data="{
        handleDrop(event) {
            const type = event.dataTransfer.getData('type');
            if (type) {
                $wire.addElement(type);
            }
        }
    }"
>
    <!-- Left Panel: Toolbox & Settings -->
    <div class="w-80 bg-white dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700/50 flex flex-col">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
            <a href="{{ route('admin.forms.index') }}" wire:navigate class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ __('navigation.forms') }}</span>
            </a>
            <flux:heading size="lg">{{ $form->getTranslation('name', 'en') }}</flux:heading>
            <flux:text variant="subtle">ID: {{ $form->id }}</flux:text>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <flux:tab.group wire:model.live="tab">
                <flux:tabs>
                    <flux:tab name="toolbox">Toolbox</flux:tab>
                    <flux:tab name="settings">Global Settings</flux:tab>
                </flux:tabs>
                <flux:tab.panel name="toolbox" class="!p-0">
                    <div class="p-4">
                        <flux:heading size="lg" class="mb-4">Toolbox</flux:heading>
                        <div class="space-y-2">
                            @foreach($elementTypes as $elementType)
                            <div
                                    class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-move hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                                draggable="true"
                                    @dragstart="event.dataTransfer.setData('type', '{{ $elementType->value }}')"
                            >
                                    <flux:button 
                                        variant="ghost" 
                                        class="w-full justify-start"
                                        tooltip="{{ $elementType->getDescription() }}"
                                    >
                                        <flux:icon name="{{ $elementType->getIcon() }}" class="size-4 mr-2" />
                                        {{ $elementType->getLabel() }}
                                    </flux:button>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </flux:tab.panel>
                <flux:tab.panel name="settings" class="!p-0">
                    <div class="space-y-4 mt-4">
                        <flux:input wire:model.live="settings.backgroundColor" type="color" label="Background Color" />
                        <flux:input wire:model.live="settings.defaultFont" label="Default Font Family" placeholder="e.g., Inter, sans-serif" />
                    </div>
                </flux:tab.panel>
            </flux:tab.group>
        </div>
    </div>

    <!-- Center Panel: Canvas -->
    <div class="flex-1 flex flex-col">
        <div class="flex justify-center items-center p-2 bg-white dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700/50 space-x-1">
            <flux:button icon="computer-desktop" wire:click="$set('activeBreakpoint', 'desktop')" :variant="$activeBreakpoint === 'desktop' ? 'primary' : 'ghost'" />
            <flux:button icon="device-tablet" wire:click="$set('activeBreakpoint', 'tablet')" :variant="$activeBreakpoint === 'tablet' ? 'primary' : 'ghost'" />
            <flux:button icon="device-phone-mobile" wire:click="$set('activeBreakpoint', 'mobile')" :variant="$activeBreakpoint === 'mobile' ? 'primary' : 'ghost'" />

            <flux:spacer />

            <div class="flex items-center gap-2">
                <flux:button 
                    wire:click="save" 
                    icon="check"
                    tooltip="Save your form changes"
                >
                    Save
                </flux:button>
                <flux:button 
                    variant="ghost" 
                    icon="eye"
                    tooltip="Preview the form as users will see it"
                >
                    Preview
                </flux:button>
            </div>
        </div>
        <div class="flex-1 p-8 overflow-y-auto" @drop.prevent="handleDrop($event)" @dragover.prevent>
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
                                    tooltip="Drag to reorder this element"
                                />
                                <flux:button 
                                    wire:click="confirmDelete('{{ $element['id'] }}', 'deleteElement')" 
                                    icon="trash" 
                                    size="xs" 
                                    variant="danger" 
                                    tooltip="Delete this element"
                                />
                            </div>
                            <div class="p-4">
                                {!! $renderedElements[$index] ?? '' !!}
                            </div>
                        </div>
                    @empty
                        <div class="text-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 col-span-full p-12 rounded-lg">
                            <flux:heading>Drop elements here</flux:heading>
                            <flux:text variant="subtle">Drag and drop from the toolbox to get started.</flux:text>
                            <flux:callout variant="secondary" icon="information-circle" class="mt-4 max-w-md mx-auto">
                                <flux:callout.text>
                                    <p>Drag form elements from the toolbox on the left and drop them here to build your form.</p>
                                    <p>You can reorder elements by dragging them within the canvas.</p>
                                </flux:callout.text>
                            </flux:callout>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Properties -->
    <div class="w-96 bg-white dark:bg-zinc-800/50 border-s border-zinc-200 dark:border-zinc-700/50 overflow-y-auto">
        @if($this->selectedElement)
            <div class="p-4" wire:key="properties-{{ $this->selectedElementId }}">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <flux:heading size="lg">Properties</flux:heading>
                        <flux:text variant="subtle">
                            {{ \App\Enums\FormElementType::tryFrom($this->selectedElement['type'])->getLabel() }}
                        </flux:text>
                    </div>
                    <flux:button wire:click="$set('selectedElementId', null)" icon="x-mark" variant="ghost" size="sm" />
                </div>
                
                <flux:accordion class="space-y-4">
                    <flux:accordion.item heading="Properties" expanded>
                <div class="space-y-4">
                            <flux:input wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.label" label="Label" />
                            <flux:input wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.placeholder" label="Placeholder" />
                    @if($this->selectedElement['type'] === 'select')
                                <flux:textarea wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.options" label="Options" help="One option per line." />
                            @endif
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item heading="Advanced Options">
                        <div class="space-y-4">
                            <flux:callout variant="secondary" icon="sparkles">
                                <flux:callout.text>
                                    Configure advanced Flux component features to enhance user experience.
                                </flux:callout.text>
                            </flux:callout>

                            @if(in_array($this->selectedElement['type'], ['text', 'textarea', 'email', 'number', 'password', 'file']))
                                <flux:heading size="sm">Input Actions</flux:heading>
                                <div class="space-y-3">
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.clearable" />
                                        <flux:label>Clearable</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.copyable" />
                                        <flux:label>Copyable</flux:label>
                                    </flux:field>
                                    @if($this->selectedElement['type'] === 'email')
                                        <flux:field variant="inline">
                                            <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.viewable" />
                                            <flux:label>Viewable (toggle visibility)</flux:label>
                                        </flux:field>
                                    @endif
                                    @if($this->selectedElement['type'] === 'password')
                                        <flux:field variant="inline">
                                            <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.viewable" />
                                            <flux:label>Show/Hide Toggle</flux:label>
                                        </flux:field>
                                    @endif
                                </div>

                                <flux:heading size="sm">Icons</flux:heading>
                                <div class="grid grid-cols-2 gap-3">
                                    @php
                                        // Build icon options HTML
                                        $leadingIconOptions = '<flux:select.option value="">No icon</flux:select.option>';
                                        $trailingIconOptions = '<flux:select.option value="">No icon</flux:select.option>';
                                        foreach ($this->availableIcons as $iconKey => $iconName) {
                                            $leadingIconOptions .= '<flux:select.option value="' . htmlspecialchars($iconKey) . '">' . htmlspecialchars($iconName) . '</flux:select.option>';
                                            $trailingIconOptions .= '<flux:select.option value="' . htmlspecialchars($iconKey) . '">' . htmlspecialchars($iconName) . '</flux:select.option>';
                                        }
                                    @endphp
                                    <flux:select 
                                        wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.icon" 
                                        label="Leading Icon"
                                        placeholder="Choose an icon..."
                                    >
                                        {!! $leadingIconOptions !!}
                                    </flux:select>
                                    <flux:select 
                                        wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.iconTrailing" 
                                        label="Trailing Icon"
                                        placeholder="Choose an icon..."
                                    >
                                        {!! $trailingIconOptions !!}
                                    </flux:select>
                                </div>
                            @endif

                            @if($this->selectedElement['type'] === 'select')
                                <flux:heading size="sm">Select Options</flux:heading>
                                <div class="space-y-3">
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.clearable" />
                                        <flux:label>Clearable</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.searchable" />
                                        <flux:label>Searchable</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.multiple" />
                                        <flux:label>Multiple Selection</flux:label>
                                    </flux:field>
                                    <flux:select 
                                        wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.variant" 
                                        label="Variant"
                                    >
                                        <flux:select.option value="default">Default</flux:select.option>
                                        <flux:select.option value="listbox">Listbox</flux:select.option>
                                        <flux:select.option value="combobox">Combobox</flux:select.option>
                                    </flux:select>
                                </div>
                            @endif

                            @if(in_array($this->selectedElement['type'], ['checkbox', 'radio']))
                                <flux:heading size="sm">Display Options</flux:heading>
                                <flux:select 
                                    wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.fluxProps.variant" 
                                    label="Variant"
                                >
                                    <flux:select.option value="default">Default</flux:select.option>
                                    <flux:select.option value="cards">Cards</flux:select.option>
                                </flux:select>
                            @endif

                            @if($this->selectedElement['type'] === 'date')
                                <flux:heading size="sm">Date Picker Options</flux:heading>
                                <div class="space-y-3">
                                    <flux:select 
                                        wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.mode" 
                                        label="Mode"
                                    >
                                        <flux:select.option value="single">Single Date</flux:select.option>
                                        <flux:select.option value="range">Date Range</flux:select.option>
                                    </flux:select>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.withPresets" />
                                        <flux:label>Show Presets</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.clearable" />
                                        <flux:label>Clearable</flux:label>
                                    </flux:field>
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.min" 
                                        label="Minimum Date" 
                                        placeholder="e.g. 2024-01-01 or today"
                                        help="Leave empty for no minimum date"
                                    />
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.max" 
                                        label="Maximum Date" 
                                        placeholder="e.g. 2030-12-31 or today"
                                        help="Leave empty for no maximum date"
                                    />
                                </div>
                            @endif

                            @if($this->selectedElement['type'] === 'number')
                                <flux:heading size="sm">Number Input Options</flux:heading>
                                <div class="space-y-3">
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.clearable" />
                                        <flux:label>Clearable</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.copyable" />
                                        <flux:label>Copyable</flux:label>
                                    </flux:field>
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.min" 
                                        type="number"
                                        label="Minimum Value" 
                                        placeholder="e.g. 0"
                                        help="Leave empty for no minimum value"
                                    />
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.max" 
                                        type="number"
                                        label="Maximum Value" 
                                        placeholder="e.g. 100"
                                        help="Leave empty for no maximum value"
                                    />
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.step" 
                                        type="number"
                                        label="Step Value" 
                                        placeholder="e.g. 1, 0.1, 10"
                                        help="Increment/decrement step for the number input"
                                    />
                                </div>
                            @endif

                            @if($this->selectedElement['type'] === 'password')
                                <flux:heading size="sm">Password Options</flux:heading>
                                <div class="space-y-3">
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.viewable" />
                                        <flux:label>Show/Hide Toggle</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.clearable" />
                                        <flux:label>Clearable</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.copyable" />
                                        <flux:label>Copyable</flux:label>
                                    </flux:field>
                                </div>
                            @endif

                            @if($this->selectedElement['type'] === 'file')
                                <flux:heading size="sm">File Upload Options</flux:heading>
                                <div class="space-y-3">
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.multiple" />
                                        <flux:label>Multiple Files</flux:label>
                                    </flux:field>
                                    <flux:field variant="inline">
                                        <flux:switch wire:model.live="elements.{{ $this->selectedElementIndex }}.properties.showPreview" />
                                        <flux:label>Show File Preview</flux:label>
                                    </flux:field>
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.accept" 
                                        label="Accepted File Types" 
                                        placeholder="e.g. .pdf,.doc,.docx or image/*"
                                        help="Comma-separated list of file extensions or MIME types"
                                    />
                                    <flux:input 
                                        wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.properties.maxSize" 
                                        label="Maximum File Size" 
                                        placeholder="e.g. 5MB, 10MB"
                                        help="Maximum allowed file size"
                                    />
                                </div>
                            @endif
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item heading="Styling">
                        <div class="space-y-4">
                            <flux:heading size="md" class="flex items-center gap-2">
                                Styles ({{ Str::title($activeBreakpoint) }})
                                <flux:tooltip toggleable>
                                    <flux:button icon="information-circle" size="sm" variant="ghost" />
                                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                        <p>Configure how this element appears on different screen sizes. The layout will automatically adjust based on the selected breakpoint.</p>
                                        <p><strong>Mobile:</strong> Phones and small tablets (up to 768px)</p>
                                        <p><strong>Tablet:</strong> Medium tablets (768px - 1024px)</p>
                                        <p><strong>Desktop:</strong> Large screens (1024px and above)</p>
                                    </flux:tooltip.content>
                                </flux:tooltip>
                            </flux:heading>
                            <flux:callout variant="secondary" icon="information-circle">
                                <flux:callout.text>
                                    Width settings for <strong>{{ Str::title($activeBreakpoint) }}</strong> breakpoint. 
                                    Elements will automatically flow in a 12-column grid system.
                                </flux:callout.text>
                            </flux:callout>
                            <div class="flex gap-2 mb-4">
                                <flux:button 
                                    wire:click="$set('activeBreakpoint', 'mobile')" 
                                    :variant="$activeBreakpoint === 'mobile' ? 'primary' : 'ghost'"
                                    size="sm"
                                    tooltip="Mobile breakpoint (up to 768px)"
                                >
                                    Mobile
                                </flux:button>
                                <flux:button 
                                    wire:click="$set('activeBreakpoint', 'tablet')" 
                                    :variant="$activeBreakpoint === 'tablet' ? 'primary' : 'ghost'"
                                    size="sm"
                                    tooltip="Tablet breakpoint (768px - 1024px)"
                                >
                                    Tablet
                                </flux:button>
                                <flux:button 
                                    wire:click="$set('activeBreakpoint', 'desktop')" 
                                    :variant="$activeBreakpoint === 'desktop' ? 'primary' : 'ghost'"
                                    size="sm"
                                    tooltip="Desktop breakpoint (1024px and above)"
                                >
                                    Desktop
                                </flux:button>
                            </div>
                            <flux:select 
                                wire:change="updateElementWidth('{{ $this->selectedElementId }}', '{{ $activeBreakpoint }}', $event.target.value)" 
                                label="Width" 
                                value="{{ $this->selectedElement['styles'][$activeBreakpoint]['width'] ?? 'full' }}"
                                wire:key="width-select-{{ $this->selectedElementId }}-{{ $activeBreakpoint }}"
                                tooltip="Choose how much horizontal space this element takes up in the 12-column grid system"
                            >
                                <flux:select.option value="full">Full Width</flux:select.option>
                                <flux:select.option value="1/2">Half Width (1/2)</flux:select.option>
                                <flux:select.option value="1/3">One Third (1/3)</flux:select.option>
                                <flux:select.option value="2/3">Two Thirds (2/3)</flux:select.option>
                                <flux:select.option value="1/4">Quarter (1/4)</flux:select.option>
                                <flux:select.option value="3/4">Three Quarters (3/4)</flux:select.option>
                            </flux:select>
                            @php
                                $currentWidth = $this->selectedElement['styles'][$activeBreakpoint]['width'] ?? 'full';
                                $widthDescription = match($currentWidth) {
                                    'full' => 'Takes up the full width (12 columns)',
                                    '1/2' => 'Takes up half the width (6 columns)',
                                    '1/3' => 'Takes up one-third width (4 columns)',
                                    '2/3' => 'Takes up two-thirds width (8 columns)',
                                    '1/4' => 'Takes up quarter width (3 columns)',
                                    '3/4' => 'Takes up three-quarters width (9 columns)',
                                    default => 'Takes up the full width (12 columns)'
                                };
                            @endphp
                            <flux:text size="sm" variant="subtle">{{ $widthDescription }}</flux:text>
                            <div class="mt-2">
                                <flux:text size="xs" variant="subtle" class="mb-2">Grid Preview:</flux:text>
                                <div class="grid grid-cols-12 gap-1 h-4">
                                    @php
                                        // Build grid cells HTML
                                        $gridCells = '';
                                        for ($i = 1; $i <= 12; $i++) {
                                            $isActive = match($currentWidth) {
                                                'full' => $i <= 12,
                                                '1/2' => $i <= 6,
                                                '1/3' => $i <= 4,
                                                '2/3' => $i <= 8,
                                                '1/4' => $i <= 3,
                                                '3/4' => $i <= 9,
                                                default => $i <= 12
                                            };
                                            $gridClass = $isActive ? 'bg-blue-500' : 'bg-zinc-200 dark:bg-zinc-700';
                                            $gridCells .= '<div class="h-full rounded-sm ' . $gridClass . '"></div>';
                                        }
                                    @endphp
                                    {!! $gridCells !!}
                                </div>
                            </div>
                            <flux:input 
                                wire:model.live.debounce="elements.{{ $this->selectedElementIndex }}.styles.{{ $activeBreakpoint }}.fontSize" 
                                label="Font Size" 
                                placeholder="e.g. 16px or 1rem"
                                tooltip="Set a custom font size for this element. Use CSS units like px, rem, em, or %"
                            />
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item heading="Validation">
                        <div class="space-y-4">
                            <flux:heading size="md" class="flex items-center gap-2">
                                Validation Rules
                                <flux:tooltip toggleable>
                                    <flux:button icon="information-circle" size="sm" variant="ghost" />
                                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                        <p>Validation rules ensure that users enter correct data when submitting the form.</p>
                                        <p>Select the rules you want to apply to this field. Some rules may require additional values (like minimum/maximum length).</p>
                                        <p>You can also customize error messages for each rule to make them more user-friendly.</p>
                                    </flux:tooltip.content>
                                </flux:tooltip>
                            </flux:heading>
                            <flux:callout variant="secondary" icon="information-circle">
                                <flux:callout.text>
                                    Select validation rules to ensure data quality. Rules will be applied when the form is submitted.
                                </flux:callout.text>
                            </flux:callout>
                            <flux:checkbox.group 
                                wire:model.live="elements.{{ $this->selectedElementIndex }}.validation.rules" 
                                label="Validation Rules" 
                                variant="pills"
                            >
                                @php
                                    // Build validation rule checkboxes HTML
                                    $validationCheckboxes = '';
                                    foreach ($this->availableValidationRules as $ruleKey => $rule) {
                                        $validationCheckboxes .= '<flux:checkbox value="' . htmlspecialchars($ruleKey) . '" label="' . htmlspecialchars($rule['label']) . '" description="' . htmlspecialchars($rule['description']) . '" icon="' . htmlspecialchars($rule['icon']) . '" />';
                                    }
                                @endphp
                                {!! $validationCheckboxes !!}
                            </flux:checkbox.group>
                            @php
                                $selectedRules = $this->selectedElement['validation']['rules'] ?? [];
                                $validationMessages = $this->selectedElement['validation']['messages'] ?? [];
                            @endphp
                            @if(!empty($selectedRules))
                                <flux:separator text="Rule Values" class="my-4" />
                                <flux:callout variant="secondary" icon="adjustments-horizontal">
                                    <flux:callout.text>
                                        Some validation rules require additional values. Set them below.
                                    </flux:callout.text>
                                </flux:callout>
                                <div class="space-y-3">
                                    @php
                                        // Build validation value inputs HTML
                                        $validationValueInputs = '';
                                        foreach ($selectedRules as $ruleKey) {
                                            if (isset($this->availableValidationRules[$ruleKey]) && ($this->availableValidationRules[$ruleKey]['has_value'] ?? false)) {
                                                $rule = $this->availableValidationRules[$ruleKey];
                                                $validationValueInputs .= '<flux:input wire:model.live.debounce="elements.' . $this->selectedElementIndex . '.validation.values.' . htmlspecialchars($ruleKey) . '" label="' . htmlspecialchars($rule['label']) . ' Value" placeholder="Enter value for ' . htmlspecialchars(strtolower($rule['label'])) . '..." help="Required value for ' . htmlspecialchars(strtolower($rule['label'])) . ' validation" />';
                                            }
                                        }
                                    @endphp
                                    {!! $validationValueInputs !!}
                                </div>
                                <flux:separator text="Custom Messages" class="my-4" />
                                <flux:callout variant="secondary" icon="chat-bubble-left-right">
                                    <flux:callout.text>
                                        Customize error messages for each validation rule. Leave empty to use default messages.
                                    </flux:callout.text>
                                </flux:callout>
                                <div class="space-y-3">
                                    @php
                                        // Build validation message inputs HTML
                                        $validationMessageInputs = '';
                                        foreach ($selectedRules as $ruleKey) {
                                            if (isset($this->availableValidationRules[$ruleKey])) {
                                                $rule = $this->availableValidationRules[$ruleKey];
                                                $validationMessageInputs .= '<flux:input wire:model.live.debounce="elements.' . $this->selectedElementIndex . '.validation.messages.' . htmlspecialchars($ruleKey) . '" label="' . htmlspecialchars($rule['label']) . ' Error Message" placeholder="Custom error message for ' . htmlspecialchars(strtolower($rule['label'])) . '..." help="Leave empty to use default message" />';
                                            }
                                        }
                                    @endphp
                                    {!! $validationMessageInputs !!}
                                </div>
                            @endif
                        </div>
                    </flux:accordion.item>
                </flux:accordion>
            </div>
        @else
            <div class="flex items-center justify-center h-full">
                <div class="text-center text-zinc-500">
                    <flux:icon name="cursor-arrow-rays" class="size-10 mx-auto" />
                    <flux:heading>Select an element</flux:heading>
                    <flux:text variant="subtle">Click on an element in the canvas to edit its properties.</flux:text>
                </div>
            </div>
        @endif
    </div>
    <style>
.sortable-ghost {
    opacity: 0.5 !important;
    background-color: rgb(59 130 246 / 0.1) !important;
    border: 2px dashed rgb(59 130 246) !important;
}
</style>
</div>


