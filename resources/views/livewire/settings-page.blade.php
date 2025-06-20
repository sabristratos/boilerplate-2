<section class="max-w-3xl">
    <x-layouts.settings.settings-sidebar :heading="$currentGroup->label" :subheading="$currentGroup->description" :groups="$groups" :currentGroup="$currentGroup">
        <div class="space-y-6">
            <form wire:submit="save" class="space-y-6">
                <div class="space-y-4">
                    {{-- Settings for this group --}}
                    @foreach($settings as $setting)
                        <div class="py-2">
                            @if(isset($setting->callout) && is_array($setting->callout))
                                <div class="mb-4">
                                    <flux:callout
                                        variant="{{ $setting->callout['variant'] ?? 'secondary' }}"
                                        icon="{{ $setting->callout['icon'] ?? null }}"
                                        text="{{ $setting->callout['text'] ?? '' }}"
                                    />
                                </div>
                            @endif

                            @switch($setting->type)
                                {{-- Text Input --}}
                                @case('text')
                                    <flux:input
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Textarea --}}
                                @case('textarea')
                                    <flux:textarea
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Checkbox --}}
                                @case('checkbox')
                                    <flux:checkbox
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Radio Group --}}
                                @case('radio')
                                    <flux:radio.group
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    >
                                        @foreach($setting->options ?? [] as $value => $label)
                                            <flux:radio value="{{ $value }}" label="{{ $label }}" />
                                        @endforeach
                                    </flux:radio.group>
                                    @break

                                {{-- Select --}}
                                @case('select')
                                    <flux:select
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    >
                                        @foreach($setting->options ?? [] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    @break

                                {{-- File Upload --}}
                                @case('file')
                                    <flux:field label="{{ $setting->label }}" description="{{ $setting->description ?? '' }}">
                                        <flux:input
                                            type="file"
                                            wire:model="files.{{ $setting->key }}"
                                        />
                                        @if($state[$setting->key] ?? null)
                                            <div class="mt-2">
                                                <flux:text>{{ __('Current file') }}: {{ basename($state[$setting->key]) }}</flux:text>
                                            </div>
                                        @endif
                                    </flux:field>
                                    @break

                                {{-- Color Picker --}}
                                @case('color')
                                    <flux:input
                                        type="color"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Date Picker --}}
                                @case('date')
                                    <flux:input
                                        type="date"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- DateTime Picker --}}
                                @case('datetime')
                                    <flux:input
                                        type="datetime-local"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Email Input --}}
                                @case('email')
                                    <flux:input
                                        type="email"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Number Input --}}
                                @case('number')
                                    <flux:input
                                        type="number"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Password Input --}}
                                @case('password')
                                    <flux:input
                                        type="password"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Range Input --}}
                                @case('range')
                                    <flux:field label="{{ $setting->label }}" description="{{ $setting->description ?? '' }}">
                                        <flux:input
                                            type="range"
                                            wire:model.live="state.{{ $setting->key }}"
                                            :value="$setting->value"
                                            min="{{ $setting->min ?? 0 }}"
                                            max="{{ $setting->max ?? 100 }}"
                                            step="{{ $setting->step ?? 1 }}"
                                        />
                                    </flux:field>
                                    @break

                                {{-- Tel Input --}}
                                @case('tel')
                                    <flux:input
                                        type="tel"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Time Input --}}
                                @case('time')
                                    <flux:input
                                        type="time"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- URL Input --}}
                                @case('url')
                                    <flux:input
                                        type="url"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                                    @break

                                {{-- Media Uploader --}}
                                @case('media')
                                    <div>
                                        <flux:label>{{ $setting->label }}</flux:label>
                                        @if($setting->description)
                                            <flux:description class="mt-2">{{ $setting->description }}</flux:description>
                                        @endif
                                        <div class="mt-2">
                                            <livewire:media-uploader :setting="$setting" :key="$setting->key" />
                                        </div>
                                    </div>
                                    @break

                                {{-- Repeater --}}
                                @case('repeater')
                                    <flux:field>
                                        <flux:label>{{ $setting->label }}</flux:label>
                                        @if($setting->description)
                                            <flux:description>{{ $setting->description }}</flux:description>
                                        @endif
                                        <div class="mt-2">
                                            <livewire:setting-repeater
                                                :settingKey="$setting->key"
                                                :subfields="$setting->subfields"
                                                :value="(array) $setting->value"
                                                :key="'repeater-' . $setting->key"
                                            />
                                        </div>
                                    </flux:field>
                                    @break

                                {{-- Default to Text Input --}}
                                @default
                                    <flux:input
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ $setting->label }}"
                                        description="{{ $setting->description ?? '' }}"
                                    />
                            @endswitch
                            </div>
                        @endforeach

                        {{-- Special actions for Advanced tab --}}
                        @if($currentGroup->key === 'advanced')
                            <div class="py-4 border-t border-gray-200 dark:border-gray-700">
                                <flux:heading size="md">{{ __('System Actions') }}</flux:heading>
                                <div class="mt-4">
                                    <flux:modal.trigger name="confirm-clear-cache">
                                        <flux:button
                                            variant="danger"
                                            icon="trash"
                                        >
                                            {{ __('Clear Application Cache') }}
                                        </flux:button>
                                    </flux:modal.trigger>
                                </div>
                            </div>
                        @endif
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end">
                    <flux:button type="submit" icon="check" variant="primary">
                        {{ __('Save Settings') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </x-layouts.settings.settings-sidebar>

    <flux:modal name="confirm-save" class="md:w-96" @cancel="$wire.cancelChanges()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="mb-2">{{ __('Are you sure?') }}</flux:heading>
                <flux:text x-html="$wire.confirmationWarning"></flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="confirmedSave" variant="primary">{{ __('Confirm') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-clear-cache" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure?') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Are you sure you want to clear the application cache?') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="clearCache" variant="danger">{{ __('Clear Cache') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
