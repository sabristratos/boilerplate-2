<x-layouts.app>
    <x-slot:title>
        {{ __('navigation.help') }}
    </x-slot:title>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">{{ __('navigation.help') }}</flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    {{ __('help.welcome_message') }}
                </flux:text>
            </div>

            <!-- Quick Start Guide -->
            <flux:card class="mb-8">
                <flux:heading size="lg" class="mb-4">{{ __('help.quick_start.title') }}</flux:heading>
                <flux:text class="mb-6">
                    {{ __('help.quick_start.description') }}
                </flux:text>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <flux:heading size="sm" class="mb-2">{{ __('help.quick_start.dashboard.title') }}</flux:heading>
                        <flux:text size="sm">
                            {{ __('help.quick_start.dashboard.description') }}
                        </flux:text>
                    </div>
                    
                    <div>
                        <flux:heading size="sm" class="mb-2">{{ __('help.quick_start.content_management.title') }}</flux:heading>
                        <flux:text size="sm">
                            {{ __('help.quick_start.content_management.description') }}
                        </flux:text>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <flux:heading size="sm" class="mb-2">{{ __('help.quick_start.pro_tip.title') }}</flux:heading>
                    <flux:text size="sm">
                        {{ __('help.quick_start.pro_tip.description') }}
                    </flux:text>
                </div>
            </flux:card>

            <!-- Main Features Section -->
            <div class="grid lg:grid-cols-2 gap-8 mb-8">
                <!-- Content Management -->
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('help.content_management.title') }}</flux:heading>
                    
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="sm" class="mb-2">{{ __('help.content_management.pages.title') }}</flux:heading>
                            <flux:text size="sm" class="mb-3">
                                {{ __('help.content_management.pages.description') }}
                            </flux:text>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                <flux:heading size="sm" class="mb-2">{{ __('help.content_management.pages.how_to_create.title') }}</flux:heading>
                                <ol class="list-decimal list-inside space-y-1 text-sm">
                                    @foreach(__('help.content_management.pages.how_to_create.steps') as $step)
                                        <li>{{ $step }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>

                        <div>
                            <flux:heading size="sm" class="mb-2">{{ __('help.content_management.forms.title') }}</flux:heading>
                            <flux:text size="sm" class="mb-3">
                                {{ __('help.content_management.forms.description') }}
                            </flux:text>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                <flux:heading size="sm" class="mb-2">{{ __('help.content_management.forms.features.title') }}</flux:heading>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach(__('help.content_management.forms.features.items') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div>
                            <flux:heading size="sm" class="mb-2">{{ __('help.content_management.media_library.title') }}</flux:heading>
                            <flux:text size="sm" class="mb-3">
                                {{ __('help.content_management.media_library.description') }}
                            </flux:text>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                <flux:heading size="sm" class="mb-2">{{ __('help.content_management.media_library.tips.title') }}</flux:heading>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach(__('help.content_management.media_library.tips.items') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Platform Tools -->
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('help.platform_tools.title') }}</flux:heading>
                    
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="sm" class="mb-2">{{ __('help.platform_tools.settings.title') }}</flux:heading>
                            <flux:text size="sm" class="mb-3">
                                {{ __('help.platform_tools.settings.description') }}
                            </flux:text>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                <flux:heading size="sm" class="mb-2">{{ __('help.platform_tools.settings.categories.title') }}</flux:heading>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach(__('help.platform_tools.settings.categories.items') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div>
                            <flux:heading size="sm" class="mb-2">{{ __('help.platform_tools.translations.title') }}</flux:heading>
                            <flux:text size="sm" class="mb-3">
                                {{ __('help.platform_tools.translations.description') }}
                            </flux:text>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                <flux:heading size="sm" class="mb-2">{{ __('help.platform_tools.translations.features.title') }}</flux:heading>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach(__('help.platform_tools.translations.features.items') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div>
                            <flux:heading size="sm" class="mb-2">{{ __('help.platform_tools.database_backup.title') }}</flux:heading>
                            <flux:text size="sm" class="mb-3">
                                {{ __('help.platform_tools.database_backup.description') }}
                            </flux:text>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                <flux:heading size="sm" class="mb-2">{{ __('help.platform_tools.database_backup.best_practices.title') }}</flux:heading>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach(__('help.platform_tools.database_backup.best_practices.items') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- Step-by-Step Tutorials -->
            <flux:card class="mb-8">
                <flux:heading size="lg" class="mb-6">{{ __('help.tutorials.title') }}</flux:heading>
                
                <flux:accordion>
                    <flux:accordion.item heading="{{ __('help.tutorials.create_first_page.title') }}">
                        <div class="space-y-3">
                            @foreach(__('help.tutorials.create_first_page.steps') as $step)
                                @if($loop->index === 2)
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.create_first_page.steps.2') }}</strong>
                                    </flux:text>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        @foreach(__('help.tutorials.create_first_page.basic_info') as $info)
                                            <li>{{ $info }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.create_first_page.steps.' . $loop->index) }}</strong>
                                        @if($loop->index !== 2)
                                            {{ $step }}
                                        @endif
                                    </flux:text>
                                @endif
                            @endforeach
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item heading="{{ __('help.tutorials.build_contact_form.title') }}">
                        <div class="space-y-3">
                            @foreach(__('help.tutorials.build_contact_form.steps') as $step)
                                @if($loop->index === 2)
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.build_contact_form.steps.2') }}</strong>
                                    </flux:text>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        @foreach(__('help.tutorials.build_contact_form.fields') as $field)
                                            <li>{{ $field }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.build_contact_form.steps.' . $loop->index) }}</strong>
                                        @if($loop->index !== 2)
                                            {{ $step }}
                                        @endif
                                    </flux:text>
                                @endif
                            @endforeach
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item heading="{{ __('help.tutorials.manage_media_library.title') }}">
                        <div class="space-y-3">
                            @foreach(__('help.tutorials.manage_media_library.steps') as $step)
                                @if($loop->index === 1)
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.manage_media_library.steps.1') }}</strong>
                                    </flux:text>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        @foreach(__('help.tutorials.manage_media_library.upload_methods') as $method)
                                            <li>{{ $method }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.manage_media_library.steps.' . $loop->index) }}</strong>
                                        @if($loop->index !== 1)
                                            {{ $step }}
                                        @endif
                                    </flux:text>
                                @endif
                            @endforeach
                        </div>
                    </flux:accordion.item>

                    <flux:accordion.item heading="{{ __('help.tutorials.configure_settings.title') }}">
                        <div class="space-y-3">
                            @foreach(__('help.tutorials.configure_settings.steps') as $step)
                                @if($loop->index === 1)
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.configure_settings.steps.1') }}</strong>
                                    </flux:text>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        @foreach(__('help.tutorials.configure_settings.general_settings') as $setting)
                                            <li>{{ $setting }}</li>
                                        @endforeach
                                    </ul>
                                @elseif($loop->index === 2)
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.configure_settings.steps.2') }}</strong>
                                    </flux:text>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        @foreach(__('help.tutorials.configure_settings.appearance_settings') as $setting)
                                            <li>{{ $setting }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <flux:text>
                                        <strong>{{ __('help.tutorials.configure_settings.steps.' . $loop->index) }}</strong>
                                        @if($loop->index !== 1 && $loop->index !== 2)
                                            {{ $step }}
                                        @endif
                                    </flux:text>
                                @endif
                            @endforeach
                        </div>
                    </flux:accordion.item>
                </flux:accordion>
            </flux:card>

            <!-- Common Tasks -->
            <flux:card class="mb-8">
                <flux:heading size="lg" class="mb-6">{{ __('help.common_tasks.title') }}</flux:heading>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <flux:heading size="sm" class="mb-3">{{ __('help.common_tasks.quick_actions.title') }}</flux:heading>
                        <div class="space-y-2">
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.quick_actions.add_page') }}</strong></flux:text>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.quick_actions.create_form') }}</strong></flux:text>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.quick_actions.upload_media') }}</strong></flux:text>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.quick_actions.change_settings') }}</strong></flux:text>
                            </div>
                        </div>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-3">{{ __('help.common_tasks.troubleshooting.title') }}</flux:heading>
                        <div class="space-y-2">
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.troubleshooting.page_not_saving') }}</strong></flux:text>
                            </div>
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.troubleshooting.form_not_working') }}</strong></flux:text>
                            </div>
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.troubleshooting.images_not_loading') }}</strong></flux:text>
                            </div>
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                <flux:text size="sm"><strong>{{ __('help.common_tasks.troubleshooting.cant_login') }}</strong></flux:text>
                            </div>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Tips and Best Practices -->
            <flux:card class="mb-8">
                <flux:heading size="lg" class="mb-6">{{ __('help.best_practices.title') }}</flux:heading>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <flux:heading size="sm" class="mb-3">{{ __('help.best_practices.security.title') }}</flux:heading>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach(__('help.best_practices.security.items') as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <flux:heading size="sm" class="mb-3">{{ __('help.best_practices.performance.title') }}</flux:heading>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach(__('help.best_practices.performance.items') as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <flux:heading size="sm" class="mb-3">{{ __('help.best_practices.seo.title') }}</flux:heading>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach(__('help.best_practices.seo.items') as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </flux:card>

            <!-- Support Information -->
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('help.support.title') }}</flux:heading>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <flux:heading size="sm" class="mb-2">{{ __('help.support.questions.title') }}</flux:heading>
                        <flux:text size="sm">
                            {{ __('help.support.questions.description') }}
                        </flux:text>
                    </div>

                    <div>
                        <flux:heading size="sm" class="mb-2">{{ __('help.support.contact.title') }}</flux:heading>
                        <div class="space-y-2 text-sm">
                            <div>{{ __('help.support.contact.email', ['email' => auth()->user()->email]) }}</div>
                            <div>{{ __('help.support.contact.response_time') }}</div>
                            <div>{{ __('help.support.contact.include_screenshots') }}</div>
                            <div>{{ __('help.support.contact.mention_username') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <flux:heading size="sm" class="mb-2">{{ __('help.support.pro_tip.title') }}</flux:heading>
                    <flux:text size="sm">
                        {{ __('help.support.pro_tip.description') }}
                    </flux:text>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app> 