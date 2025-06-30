<div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700 shadow-lg">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center">
            <!-- Logo -->
            <div class="flex items-center gap-4">
                <div x-data="{ open: false }" @keydown.window.escape="open = false" class="relative">
                    <a href="{{ route('home') }}" class="block">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <x-app-logo class="h-8 w-8" />
                    </a>
                </div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-zinc-800 dark:text-white font-semibold">
                    <span class="hidden sm:inline">{{ $siteName }}</span>
                </a>
            </div>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center gap-x-1">
                @can('edit pages')
                    <flux:button
                        href="{{ route('admin.pages.index') }}"
                        icon="document-text"
                        variant="subtle"
                    >
                        {{ __('navigation.pages') }}
                    </flux:button>
                @endcan
                @can('edit content')
                    <flux:button
                        href="{{ route('admin.media.index') }}"
                        icon="photo"
                        variant="subtle"
                    >
                        {{ __('navigation.media_library') }}
                    </flux:button>
                @endcan
                @foreach($resources as $resource)
                    @can($resource::$permission)
                        <flux:button
                            href="{{ route('admin.resources.' . $resource::uriKey() . '.index') }}"
                            icon="{{ $resource::$navigationIcon }}"
                            variant="subtle"
                        >
                            {{ $resource::pluralLabel() }}
                        </flux:button>
                    @endcan
                @endforeach
            </div>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Right side actions -->
            <div class="flex items-center gap-4">
                <!-- Desktop User Dropdown -->
                <div class="hidden md:block">
                    <flux:dropdown position="top" align="end">
                        <flux:avatar
                            as="button"
                            src="{{ auth()->user()->avatar_url }}"
                            :name="auth()->user()->name"
                            size="sm"
                            circle
                        />
                        <flux:menu>
                            <flux:menu.group heading="{{ __('navigation.settings_group_heading') }}">
                                <flux:menu.item href="{{ route('admin.translations.index') }}" icon="language">{{ __('navigation.translations') }}</flux:menu.item>
                                <flux:menu.item href="{{ route('admin.settings.group', 'general') }}" icon="cog-6-tooth">{{ __('navigation.settings') }}</flux:menu.item>
                            </flux:menu.group>
                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <flux:menu.item
                                    as="button"
                                    type="submit"
                                    variant="danger"
                                    icon="arrow-left-on-rectangle"
                                >
                                    {{ __('auth.logout') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <!-- Mobile Dropdown -->
                <div class="md:hidden">
                    <flux:dropdown position="top" align="end">
                        <flux:button icon="bars-3" variant="ghost" />
                        <flux:menu>
                            <flux:menu.item href="{{ route('dashboard') }}" icon="layout-grid">{{ __('navigation.dashboard') }}</flux:menu.item>
                            <flux:menu.group heading="{{ __('navigation.content_group_heading') }}">
                                @can('edit pages')
                                    <flux:menu.item href="{{ route('admin.pages.index') }}" icon="document-text">{{ __('navigation.pages') }}</flux:menu.item>
                                @endcan
                                @can('edit content')
                                    <flux:menu.item href="{{ route('admin.media.index') }}" icon="photo">{{ __('navigation.media_library') }}</flux:menu.item>
                                @endcan
                            </flux:menu.group>

                            @if(count($resources) > 0)
                                <flux:menu.group heading="{{ __('navigation.resources_group_heading') }}">
                                    @foreach($resources as $resource)
                                        @can($resource::$permission)
                                            <flux:menu.item href="{{ route('admin.resources.' . $resource::uriKey() . '.index') }}" icon="{{ $resource::$navigationIcon }}">
                                                {{ $resource::pluralLabel() }}
                                            </flux:menu.item>
                                        @endcan
                                    @endforeach
                                </flux:menu.group>
                            @endif

                            <flux:menu.group heading="{{ __('navigation.settings_group_heading') }}">
                                <flux:menu.item href="{{ route('admin.translations.index') }}" icon="language">{{ __('navigation.translations') }}</flux:menu.item>
                                <flux:menu.item href="{{ route('admin.settings.group', 'general') }}" icon="cog-6-tooth">{{ __('navigation.settings') }}</flux:menu.item>
                            </flux:menu.group>
                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <flux:menu.item
                                    as="button"
                                    type="submit"
                                    variant="danger"
                                    icon="arrow-left-on-rectangle"
                                >
                                    {{ __('auth.logout') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </div>
    </div>
</div> 