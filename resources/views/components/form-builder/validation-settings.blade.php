@props(['selectedElement', 'selectedElementIndex', 'availableValidationRules'])
<div class="space-y-4">
    <flux:heading size="md" class="flex items-center gap-2">
        <flux:icon name="shield-check" class="size-5" />
        Validation Rules
        <flux:tooltip toggleable>
            <flux:button icon="information-circle" size="sm" variant="ghost" />
            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                <p>Configure validation rules for this field. Enter values to activate validation rules.</p>
                <p>Leave fields empty to disable validation rules.</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </flux:heading>

    @if(empty($availableValidationRules))
        <flux:callout variant="secondary" icon="information-circle">
            <flux:callout.text>
                No validation rules available for this field type.
            </flux:callout.text>
        </flux:callout>
    @else
        @php
            $groupedRules = collect($availableValidationRules)->groupBy('category');
            $selectedRules = $selectedElement['validation']['rules'] ?? [];
            $ruleValues = $selectedElement['validation']['values'] ?? [];
            $customMessages = $selectedElement['validation']['messages'] ?? [];
        @endphp

        <!-- Required Field Toggle -->
        <div class="space-y-3">
            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Basic</flux:heading>
            <flux:field variant="inline">
                <flux:checkbox 
                    wire:click="toggleValidationRule('{{ $selectedElementIndex }}', 'required')" 
                    value="required"
                    :checked="in_array('required', $selectedRules)"
                />
                <flux:label class="flex items-center gap-2">
                    <flux:icon name="exclamation-triangle" class="size-4" />
                    Required Field
                    <flux:tooltip>
                        <flux:button icon="information-circle" size="xs" variant="ghost" />
                        <flux:tooltip.content>Field must be filled out</flux:tooltip.content>
                    </flux:tooltip>
                </flux:label>
            </flux:field>
            @if(in_array('required', $selectedRules))
                                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.required" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
            @endif
        </div>

        <!-- Length Validation: Min/Max always visible and grouped -->
        @if(isset($groupedRules['Length']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Length</flux:heading>
                
                <div class="flex gap-2">
                    @foreach($groupedRules['Length'] as $ruleKey => $rule)
                        @if($rule['has_value'])
                            <flux:input 
                                wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.values.{{ $ruleKey }}" 
                                label="{{ $rule['label'] }}" 
                                placeholder="{{ $this->getValidationPlaceholder($ruleKey) }}"
                                size="sm"
                                help="{{ $rule['description'] }}"
                                type="number"
                                min="1"
                                class="w-1/2"
                            />
                        @endif
                    @endforeach
                </div>
                @foreach($groupedRules['Length'] as $ruleKey => $rule)
                    @if($rule['has_value'] && !empty($ruleValues[$ruleKey]))
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Format Validation: regex and others -->
        @if(isset($groupedRules['Format']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Format</flux:heading>
                @foreach($groupedRules['Format'] as $ruleKey => $rule)
                    @if($ruleKey === 'regex')
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.values.regex" 
                            label="Custom Pattern (Regex)" 
                            placeholder="{{ $this->getValidationPlaceholder('regex') }}"
                            size="sm"
                            help="{{ $rule['description'] }}"
                        />
                        @if(!empty($ruleValues['regex']))
                            <flux:input 
                                wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.regex" 
                                label="Custom Message (optional)" 
                                placeholder="Custom error message..."
                                size="sm"
                                help="Leave empty to use default message"
                            />
                        @endif
                    @elseif(!$rule['has_value'])
                        <flux:field variant="inline">
                            <flux:checkbox 
                                wire:click="toggleValidationRule('{{ $selectedElementIndex }}', '{{ $ruleKey }}')" 
                                value="{{ $ruleKey }}"
                                :checked="in_array('{{ $ruleKey }}', $selectedRules)"
                            />
                            <flux:label class="flex items-center gap-2">
                                <flux:icon name="{{ $rule['icon'] }}" class="size-4" />
                                {{ $rule['label'] }}
                                <flux:tooltip>
                                    <flux:button icon="information-circle" size="xs" variant="ghost" />
                                    <flux:tooltip.content>{{ $rule['description'] }}</flux:tooltip.content>
                                </flux:tooltip>
                            </flux:label>
                        </flux:field>
                        @if(in_array($ruleKey, $selectedRules))
                            <flux:input 
                                wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                                label="Custom Message (optional)" 
                                placeholder="Custom error message..."
                                size="sm"
                                help="Leave empty to use default message"
                            />
                        @endif
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Range Validation: min_value/max_value always visible and grouped -->
        @if(isset($groupedRules['Range']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Range</flux:heading>
                <div class="flex gap-2">
                    @foreach(['min_value', 'max_value'] as $ruleKey)
                        @if(isset($groupedRules['Range'][$ruleKey]))
                            <flux:input 
                                wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.values.{{ $ruleKey }}" 
                                label="{{ $groupedRules['Range'][$ruleKey]['label'] }}" 
                                placeholder="{{ $this->getValidationPlaceholder($ruleKey) }}"
                                size="sm"
                                help="{{ $groupedRules['Range'][$ruleKey]['description'] }}"
                                type="number"
                                class="w-1/2"
                            />
                        @endif
                    @endforeach
                </div>
                @foreach(['min_value', 'max_value'] as $ruleKey)
                    @if(isset($groupedRules['Range'][$ruleKey]) && !empty($ruleValues[$ruleKey]))
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
                    @endif
                @endforeach
                @foreach($groupedRules['Range'] as $ruleKey => $rule)
                    @if(!$rule['has_value'])
                        <flux:field variant="inline">
                            <flux:checkbox 
                                wire:click="toggleValidationRule('{{ $selectedElementIndex }}', '{{ $ruleKey }}')" 
                                value="{{ $ruleKey }}"
                                :checked="in_array('{{ $ruleKey }}', $selectedRules)"
                            />
                            <flux:label class="flex items-center gap-2">
                                <flux:icon name="{{ $rule['icon'] }}" class="size-4" />
                                {{ $rule['label'] }}
                                <flux:tooltip>
                                    <flux:button icon="information-circle" size="xs" variant="ghost" />
                                    <flux:tooltip.content>{{ $rule['description'] }}</flux:tooltip.content>
                                </flux:tooltip>
                            </flux:label>
                        </flux:field>
                        @if(in_array($ruleKey, $selectedRules))
                            <flux:input 
                                wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                                label="Custom Message (optional)" 
                                placeholder="Custom error message..."
                                size="sm"
                                help="Leave empty to use default message"
                            />
                        @endif
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Date Range Validation: always visible -->
        @if(isset($groupedRules['Date Range']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Date Range</flux:heading>
                @foreach($groupedRules['Date Range'] as $ruleKey => $rule)
                    <flux:input 
                        wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.values.{{ $ruleKey }}" 
                        label="{{ $rule['label'] }}" 
                        placeholder="{{ $this->getValidationPlaceholder($ruleKey) }}"
                        size="sm"
                        help="{{ $rule['description'] }}"
                        type="date"
                    />
                    @if(!empty($ruleValues[$ruleKey]))
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Security Validation -->
        @if(isset($groupedRules['Security']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Security</flux:heading>
                @foreach($groupedRules['Security'] as $ruleKey => $rule)
                    <flux:field variant="inline">
                        <flux:checkbox 
                            wire:click="toggleValidationRule('{{ $selectedElementIndex }}', '{{ $ruleKey }}')" 
                            value="{{ $ruleKey }}"
                            :checked="in_array('{{ $ruleKey }}', $selectedRules)"
                        />
                        <flux:label class="flex items-center gap-2">
                            <flux:icon name="{{ $rule['icon'] }}" class="size-4" />
                            {{ $rule['label'] }}
                            <flux:tooltip>
                                <flux:button icon="information-circle" size="xs" variant="ghost" />
                                <flux:tooltip.content>{{ $rule['description'] }}</flux:tooltip.content>
                            </flux:tooltip>
                        </flux:label>
                    </flux:field>
                    @if(in_array($ruleKey, $selectedRules))
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Size Validation: always visible -->
        @if(isset($groupedRules['Size']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Size</flux:heading>
                @foreach($groupedRules['Size'] as $ruleKey => $rule)
                    <flux:input 
                        wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.values.{{ $ruleKey }}" 
                        label="{{ $rule['label'] }}" 
                        placeholder="{{ $this->getValidationPlaceholder($ruleKey) }}"
                        size="sm"
                        help="{{ $rule['description'] }}"
                        type="number"
                        min="1"
                    />
                    @if(!empty($ruleValues[$ruleKey]))
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Advanced Validation: always visible -->
        @if(isset($groupedRules['Advanced']))
            <div class="space-y-3">
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">Advanced</flux:heading>
                @foreach($groupedRules['Advanced'] as $ruleKey => $rule)
                    <flux:input 
                        wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.values.{{ $ruleKey }}" 
                        label="{{ $rule['label'] }}" 
                        placeholder="{{ $this->getValidationPlaceholder($ruleKey) }}"
                        size="sm"
                        help="{{ $rule['description'] }}"
                    />
                    @if(!empty($ruleValues[$ruleKey]))
                        <flux:input 
                            wire:model.live.debounce.500ms="draftElements.{{ $selectedElementIndex }}.validation.messages.{{ $ruleKey }}" 
                            label="Custom Message (optional)" 
                            placeholder="Custom error message..."
                            size="sm"
                            help="Leave empty to use default message"
                        />
                    @endif
                @endforeach
            </div>
        @endif

    @endif
</div> 
