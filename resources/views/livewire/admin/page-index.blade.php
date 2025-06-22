<div>
    <flux:card class="p-0">
        <div class="p-6 flex items-center justify-between">
            <flux:heading>Pages</flux:heading>
            <a href="{{ route('admin.pages.create') }}" wire:navigate>
                <flux:button>New page</flux:button>
            </a>
        </div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Page</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($this->pages as $page)
                    <flux:table.row :key="$page->id">
                        <flux:table.cell>
                            <div>
                                <flux:text class="font-semibold">{{ $page->title }}</flux:text>
                                <flux:text variant="subtle" class="text-xs">{{ $page->slug }}</flux:text>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <a href="{{ route('admin.pages.editor', $page) }}" wire:navigate>
                                <flux:button variant="subtle">Edit</flux:button>
                            </a>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
