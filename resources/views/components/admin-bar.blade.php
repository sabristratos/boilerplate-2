<div class="fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700 shadow-lg">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center">
            <!-- Logo -->
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-zinc-800 dark:text-white font-semibold">
                    @if ($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto">
                    @else
                        <x-app-logo-icon class="h-8 w-8" />
                    @endif
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
                        Pages
                    </flux:button>
                @endcan
                @can('edit content')
                    <flux:button
                        href="{{ route('media.index') }}"
                        icon="photo"
                        variant="subtle"
                    >
                        Media Library
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
                            <flux:menu.group heading="Settings">
                                <flux:menu.item href="{{ route('admin.translations.index') }}" icon="language">Translations</flux:menu.item>
                                <flux:menu.item href="{{ route('settings.group', 'general') }}" icon="cog-6-tooth">Settings</flux:menu.item>
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
                                    Logout
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
                            <flux:menu.item href="{{ route('dashboard') }}" icon="layout-grid">Dashboard</flux:menu.item>
                            <flux:menu.group heading="Content">
                                @can('edit pages')
                                    <flux:menu.item href="{{ route('admin.pages.index') }}" icon="document-text">Pages</flux:menu.item>
                                @endcan
                                @can('edit content')
                                    <flux:menu.item href="{{ route('media.index') }}" icon="photo">Media Library</flux:menu.item>
                                @endcan
                            </flux:menu.group>

                            @if(count($resources) > 0)
                                <flux:menu.group heading="Resources">
                                    @foreach($resources as $resource)
                                        @can($resource::$permission)
                                            <flux:menu.item href="{{ route('admin.resources.' . $resource::uriKey() . '.index') }}" icon="{{ $resource::$navigationIcon }}">
                                                {{ $resource::pluralLabel() }}
                                            </flux:menu.item>
                                        @endcan
                                    @endforeach
                                </flux:menu.group>
                            @endif

                            <flux:menu.group heading="Settings">
                                <flux:menu.item href="{{ route('admin.translations.index') }}" icon="language">Translations</flux:menu.item>
                                <flux:menu.item href="{{ route('settings.group', 'general') }}" icon="cog-6-tooth">Settings</flux:menu.item>
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
                                    Logout
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </div>
    </div>
</div> 