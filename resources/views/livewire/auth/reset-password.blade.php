<div class="space-y-6">
    <x-auth-header
        :title="__('auth.reset_password_title')"
        :description="__('auth.reset_password_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="resetPassword" class="space-y-6">
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
            {{ __('buttons.reset_password') }}
        </flux:button>
    </form>
</div>
