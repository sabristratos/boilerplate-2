<flux:menu class="w-64">
    <div class="p-2">
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                size="sm"
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                <span class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</span>
            </div>
        </div>
    </div>

    <flux:menu.separator />

    @livewire('theme-switcher')

    <flux:menu.separator />

    @livewire('locale-switcher')

    <flux:menu.separator />

    <form method="POST" action="{{ route('logout') }}" class="p-2">
        @csrf
        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('buttons.logout') }}
        </flux:menu.item>
    </form>
</flux:menu> 