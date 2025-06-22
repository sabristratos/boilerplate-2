<div class="space-y-6">
    <x-auth-header
        :title="__('auth.register_title')"
        :description="__('auth.register_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="register" class="space-y-6">
        <flux:field :label="__('labels.name')">
            <flux:input
                wire:model="name"
                type="text"
                required
                autofocus
            />
        </flux:field>

        <flux:field :label="__('labels.email')">
            <flux:input
                wire:model="email"
                type="email"
                required
            />
        </flux:field>

        <flux:field :label="__('labels.password')">
            <flux:input
                wire:model="password"
                type="password"
                required
            />
        </flux:field>

        <flux:field :label="__('labels.password_confirmation')">
            <flux:input
                wire:model="password_confirmation"
                type="password"
                required
            />
        </flux:field>

        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
        >
            {{ __('buttons.create_account') }}
        </flux:button>
    </form>

    <div class="text-center">
        <flux:text size="sm">
            {{ __('auth.login_prompt') }}
            <flux:button
                href="{{ route('login') }}"
                variant="link"
            >
                {{ __('buttons.login') }}
            </flux:button>
        </flux:text>
    </div>
</div>
