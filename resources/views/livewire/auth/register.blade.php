<div class="space-y-6">
    <x-auth-header
        :title="__('auth.register_title')"
        :description="__('auth.register_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="register" class="space-y-6">
        <flux:input
            wire:model="name"
            type="text"
            :label="__('labels.name')"
            required
            autofocus
        />

        <flux:input
            wire:model="email"
            type="email"
            :label="__('labels.email')"
            required
        />

        <flux:input
            wire:model="password"
            type="password"
            :label="__('labels.password')"
            required
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            type="password"
            :label="__('labels.password_confirmation')"
            required
            viewable
        />

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
            <flux:link href="{{ route('login') }}">
                {{ __('buttons.login') }}
            </flux:link>
        </flux:text>
    </div>
</div>
