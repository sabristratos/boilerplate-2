<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            @foreach($groups as $navGroup)
                <flux:navlist.item
                    :href="route('settings.group', ['group' => $navGroup->key])"
                    :current="$navGroup->key === $currentGroup->key"
                    wire:navigate
                    icon="{{ $navGroup->icon }}"
                >
                    {{ $navGroup->label }}
                </flux:navlist.item>
            @endforeach
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>
