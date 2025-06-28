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
                                        variant="{{ $setting->callout['variant'] ?? 'outline' }}"
                                        icon="{{ $setting->callout['icon'] ?? null }}"
                                    >
                                        {{ __($setting->callout['text'] ?? '') }}
                                    </flux:callout>
                                </div>
                            @endif

                            @switch($setting->type)
                                {{-- Text Input --}}
                                @case('text')
                                    <flux:input
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Textarea --}}
                                @case('textarea')
                                    <flux:textarea
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Checkbox --}}
                                @case('checkbox')
                                    <flux:checkbox
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Radio Group --}}
                                @case('radio')
                                    <flux:radio.group
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    >
                                        @foreach($setting->options ?? [] as $value => $label)
                                            <flux:radio value="{{ $value }}" label="{{ __($label) }}" />
                                        @endforeach
                                    </flux:radio.group>
                                    @break

                                {{-- Select --}}
                                @case('select')
                                    @if ($setting->key === 'general.default_locale' || $setting->key === 'general.fallback_locale')
                                        @php
                                            $localesSetting = data_get($state, 'general.available_locales');
                                            $localeOptions = collect($localesSetting)->pluck('name', 'code')->all();
                                        @endphp
                                        <flux:select
                                            wire:model.live="state.{{ $setting->key }}"
                                            label="{{ __($setting->label) }}"
                                            description="{{ __($setting->description ?? '') }}"
                                        >
                                            @foreach($localeOptions as $value => $label)
                                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    @elseif ($setting->key === 'general.homepage')
                                        <flux:select
                                            wire:model.live="state.{{ $setting->key }}"
                                            label="{{ __($setting->label) }}"
                                            description="{{ __($setting->description ?? '') }}"
                                        >
                                            @forelse($setting->options ?? [] as $value => $label)
                                                <flux:select.option value="{{ $value }}">{{ __($label) }}</flux:select.option>
                                            @empty
                                                <flux:select.option value="" disabled>{{ __('messages.no_pages_found') }}</flux:select.option>
                                            @endforelse
                                        </flux:select>
                                    @else
                                        <flux:select
                                            wire:model.live="state.{{ $setting->key }}"
                                            :value="$setting->value"
                                            label="{{ __($setting->label) }}"
                                            description="{{ __($setting->description ?? '') }}"
                                        >
                                            @foreach($setting->options ?? [] as $value => $label)
                                                <flux:select.option value="{{ $value }}">{{ __($label) }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    @endif
                                    @break

                                {{-- File Upload --}}
                                @case('file')
                                    <flux:field label="{{ __($setting->label) }}" description="{{ __($setting->description ?? '') }}">
                                        <flux:input
                                            type="file"
                                            wire:model="files.{{ $setting->key }}"
                                        />
                                        @if($state[$setting->key] ?? null)
                                            <div class="mt-2">
                                                <flux:text>{{ __('labels.current_file') }}: {{ basename($state[$setting->key]) }}</flux:text>
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
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Date Picker --}}
                                @case('date')
                                    <flux:input
                                        type="date"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- DateTime Picker --}}
                                @case('datetime')
                                    <flux:input
                                        type="datetime-local"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Email Input --}}
                                @case('email')
                                    <flux:input
                                        type="email"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Number Input --}}
                                @case('number')
                                    <flux:input
                                        type="number"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Password Input --}}
                                @case('password')
                                    <flux:input
                                        type="password"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Range Input --}}
                                @case('range')
                                    <flux:field label="{{ __($setting->label) }}" description="{{ __($setting->description ?? '') }}">
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
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Time Input --}}
                                @case('time')
                                    <flux:input
                                        type="time"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- URL Input --}}
                                @case('url')
                                    <flux:input
                                        type="url"
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                                    @break

                                {{-- Media Uploader --}}
                                @case('media')
                                    <div>
                                        <flux:label>{{ __($setting->label) }}</flux:label>
                                        @if($setting->description)
                                            <flux:description class="mt-2">{{ __($setting->description) }}</flux:description>
                                        @endif
                                        <div class="mt-2">
                                            <livewire:media-uploader :model="$setting" :key="$setting->key" />
                                        </div>
                                    </div>
                                    @break

                                {{-- Repeater --}}
                                @case('repeater')
                                    <x-settings.repeater :setting="$setting" />
                                    @break

                                {{-- Default to Text Input --}}
                                @default
                                    <flux:input
                                        wire:model.live="state.{{ $setting->key }}"
                                        :value="$setting->value"
                                        label="{{ __($setting->label) }}"
                                        description="{{ __($setting->description ?? '') }}"
                                    />
                            @endswitch
                            </div>
                        @endforeach

                        {{-- Special actions for General tab --}}
                        @if($currentGroup->key === 'general')
                            <div class="py-2">
                                <flux:field label="Fix Language Settings" description="Reset language settings to defaults if you're experiencing issues with language dropdowns or available locales.">
                                    <flux:button
                                        type="button"
                                        variant="danger"
                                        wire:click="fixLanguageSettings"
                                    >
                                        Reset Language Settings
                                    </flux:button>
                                </flux:field>
                            </div>
                        @endif

                        {{-- Special actions for Advanced tab --}}
                        @if($currentGroup->key === 'advanced')
                            <div class="py-2">
                                <flux:field :label="__('labels.clear_cache_action')" :description="__('labels.clear_cache_action_desc')">
                                    <flux:button
                                        type="button"
                                        variant="danger"
                                        wire:click="clearCache"
                                    >
                                        {{ __('buttons.clear_cache') }}
                                    </flux:button>
                                </flux:field>
                            </div>
                        @endif
                </div>

                <div class="flex items-center justify-between border-t border-zinc-200 pt-6 dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        <flux:button type="submit" variant="primary">
                            {{ __('buttons.save_settings') }}
                        </flux:button>

                        <x-action-message class="me-3" on="settings-updated">
                            {{ __('messages.saved') }}
                        </x-action-message>
                    </div>
                </div>
            </form>

            <flux:modal name="confirm-save" :title="__('messages.confirm_save.title')">
                <div x-html="$wire.confirmationWarning"></div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button variant="outline" wire:click="$dispatch('close', { id: 'confirm-save' })">
                        {{ __('buttons.cancel') }}
                    </flux:button>
                    <flux:button wire:click="confirmedSave" variant="primary">
                        {{ __('buttons.confirm_save') }}
                    </flux:button>
                </div>
            </flux:modal>

        </div>
    </x-layouts.settings.settings-sidebar>
</section>
