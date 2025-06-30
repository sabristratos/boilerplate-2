<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <flux:heading>Forms</flux:heading>
                <flux:text>A list of all the forms in your account.</flux:text>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <flux:button wire:click="openCreateModal" variant="primary">Add form</flux:button>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    @if($forms->count())
                        <ul role="list" class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($forms as $form)
                                <li wire:key="form-{{ $form->id }}" class="col-span-1 flex flex-col divide-y divide-zinc-200 dark:divide-zinc-700 rounded-lg bg-white dark:bg-zinc-800/50 shadow">
                                    <div class="flex flex-1 flex-col p-8">
                                        <flux:heading size="lg">{{ $form->getTranslation('name', 'en') }}</flux:heading>
                                        <dl class="mt-1 flex flex-grow flex-col justify-between">
                                            <dd class="text-sm text-zinc-500 dark:text-zinc-400">{{ $form->elements ? count($form->elements) : 0 }} elements</dd>
                                            <dd class="text-sm text-zinc-500 dark:text-zinc-400">{{ $form->submissions()->count() }} submissions</dd>
                                            <dd class="mt-3">
                                                <flux:badge color="green">{{ $form->status ?? 'Draft' }}</flux:badge>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div>
                                        <div class="-mt-px flex divide-x divide-zinc-200 dark:divide-zinc-700">
                                            <div class="flex w-0 flex-1">
                                                <flux:button
                                                    class="!rounded-none !rounded-bl-lg w-full justify-center"
                                                    variant="ghost"
                                                    href="{{ route('admin.forms.edit', $form) }}"
                                                    wire:navigate
                                                >
                                                    Edit
                                                </flux:button>
                                            </div>
                                            <div class="-ml-px flex w-0 flex-1">
                                                <flux:button
                                                    class="!rounded-none w-full justify-center"
                                                    variant="ghost"
                                                    href="{{ route('admin.forms.submissions', $form) }}"
                                                    wire:navigate
                                                >
                                                    Submissions
                                                </flux:button>
                                            </div>
                                            <div class="-ml-px flex w-0 flex-1">
                                                <flux:button
                                                    class="!rounded-none !rounded-br-lg w-full justify-center"
                                                    variant="ghost"
                                                    href="#"
                                                >
                                                    View
                                                </flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-6">
                            {{ $forms->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                             <flux:icon name="document-text" class="mx-auto h-12 w-12 text-zinc-400" />
                            <flux:heading>No forms</flux:heading>
                            <flux:text>Get started by creating a new form.</flux:text>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <flux:modal name="create-form">
        <flux:card>
            <form wire:submit.prevent="create">
                <flux:heading>Create a new form</flux:heading>
                <div class="mt-4">
                    <flux:input wire:model="newFormName" label="Form Name" placeholder="e.g. Contact Us" />
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button variant="ghost" @click="$flux.modal('create-form').close()">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Create</flux:button>
                </div>
            </form>
        </flux:card>
    </flux:modal>
</div>
