@php
    $logoUrl = setting_media_url('appearance.logo');
@endphp

<div class="app-logo-container flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ setting('general.app_name', 'Stratos Built') }}" class="app-logo-image size-8 object-contain" />
    @else
        <x-app-logo-icon class="app-logo-icon size-5 fill-current text-white dark:text-black" />
    @endif
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="app-logo-name mb-0.5 truncate leading-tight font-semibold">{{ setting('general.app_name', 'Stratos Built') }}</span>
</div>
