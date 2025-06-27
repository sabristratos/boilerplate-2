<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group class="grid">

                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('navigation.dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="photo" :href="route('media.index')" :current="request()->routeIs('media.*')" wire:navigate>{{ __('navigation.media_library') }}</flux:navlist.item>
                    <flux:navlist.item icon="language" :href="route('admin.translations.index')" :current="request()->routeIs('admin.translations.*')" wire:navigate>{{ __('navigation.translations') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.pages.index')" :current="request()->routeIs('admin.pages.*')" wire:navigate>{{ __('navigation.pages') }}</flux:navlist.item>
                    <flux:navlist.item icon="rectangle-group" :href="route('admin.forms.index')" :current="request()->routeIs('admin.forms.*')" wire:navigate>{{ __('navigation.forms') }}</flux:navlist.item>
                </flux:navlist.group>


                @if(isset($resources) && count($resources) > 0)
                <flux:navlist.group :heading="__('navigation.resources')" class="grid">
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
                <flux:navlist.group :heading="__('navigation.platform')" class="grid">
                    <flux:menu.item :href="route('settings.group', ['group' => 'general'])" icon="cog" wire:navigate>{{ __('navigation.settings') }}</flux:menu.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="top" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />
                <x-layouts.app.user-menu />
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />
                <x-layouts.app.user-menu />
            </flux:dropdown>
        </flux:header>

        {{ $slot }}


        @persist('toast')
        <flux:toast position="bottom right" />
        @endpersist
        @fluxScripts
    </body>
</html>
