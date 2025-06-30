<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        @foreach ($stats as $stat)
            <flux:card>
                <div class="flex items-center gap-2">
                    <flux:icon name="{{ $stat['icon'] }}" class="size-6 text-neutral-500" />
                    <flux:text variant="strong">
                        {{ __($stat['name']) }}
                    </flux:text>
                </div>
                <div class="mt-4 text-3xl font-bold">
                    {{ $stat['value'] }}
                </div>
            </flux:card>
        @endforeach
    </div>

</div>
