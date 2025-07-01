<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <!-- Brand/Logo -->
            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <!-- Search -->
            <flux:input as="button" variant="filled" placeholder="{{ __('navigation.search') }}..." icon="magnifying-glass" />

            <!-- Main Navigation -->
            <flux:navlist variant="outline">
                <!-- Dashboard -->
                <flux:navlist.item 
                    icon="home" 
                    :href="route('dashboard')" 
                    :current="request()->routeIs('dashboard')" 
                    wire:navigate
                >
                    {{ __('navigation.dashboard') }}
                </flux:navlist.item>

                <!-- Content Management -->
                <flux:navlist.group expandable heading="{{ __('navigation.content_group_heading') }}" class="hidden lg:grid">
                    <flux:navlist.item 
                        icon="document-text" 
                        :href="route('admin.pages.index')" 
                        :current="request()->routeIs('admin.pages.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.pages') }}
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="document-duplicate" 
                        :href="route('admin.forms.index')" 
                        :current="request()->routeIs('admin.forms.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.forms') }}
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="photo" 
                        :href="route('admin.media.index')" 
                        :current="request()->routeIs('admin.media.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.media_library') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <!-- Resources -->
                @if(isset($resources) && count($resources) > 0)
                <flux:navlist.group expandable heading="{{ __('navigation.resources_group_heading') }}" class="hidden lg:grid">
                    @foreach($resources as $resource)
                        @php
                            $resourceClass = new $resource();
                            $uriKey = $resource::uriKey();
                        @endphp
                        <flux:navlist.item
                            icon="{{ str_replace('heroicon-o-', '', $resource::$navigationIcon) }}"
                            :href="route('admin.resources.'.$uriKey.'.index')"
                            :current="request()->routeIs('admin.resources.'.$uriKey.'.*')"
                            wire:navigate
                        >
                            {{ $resource::pluralLabel() }}
                        </flux:navlist.item>
                    @endforeach
                </flux:navlist.group>
                @endif

                <!-- Analytics & Reports -->
                <flux:navlist.group expandable heading="Analytics" class="hidden lg:grid">
                    <flux:navlist.item 
                        icon="chart-bar" 
                        :href="route('admin.analytics.index')" 
                        :current="request()->routeIs('admin.analytics.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.analytics') }}
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="document-chart-bar" 
                        :href="route('admin.reports.index')" 
                        :current="request()->routeIs('admin.reports.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.reports') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <!-- Platform -->
                <flux:navlist.group expandable heading="{{ __('navigation.platform') }}" class="hidden lg:grid">
                    <flux:navlist.item 
                        :href="route('admin.settings.group', ['group' => 'general'])" 
                        icon="cog-6-tooth" 
                        wire:navigate
                    >
                        {{ __('navigation.settings') }}
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="language" 
                        :href="route('admin.translations.index')" 
                        :current="request()->routeIs('admin.translations.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.translations') }}
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="question-mark-circle" 
                        :href="route('admin.help.index')" 
                        :current="request()->routeIs('admin.help.*')" 
                        wire:navigate
                    >
                        {{ __('navigation.help') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <!-- User Menu -->
            <flux:dropdown class="hidden lg:block" position="top" align="start">
                @if(auth()->check())
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />
                <x-layouts.app.user-menu />
                @endif
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            @if(auth()->check())
            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevron-down"
                />
                <x-layouts.app.user-menu />
            </flux:dropdown>
            @endif
        </flux:header>

        {{ $slot }}

        @livewire('confirmation-modal')  
        @persist('toast')
        <flux:toast position="bottom right" />
        @endpersist
        @fluxScripts
    </body>
</html>
