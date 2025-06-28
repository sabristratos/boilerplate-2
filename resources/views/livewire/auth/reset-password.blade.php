<div class="space-y-6">
    <x-auth-header
        :title="__('auth.reset_password_title')"
        :description="__('auth.reset_password_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="resetPassword" class="space-y-6">
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
            {{ __('buttons.reset_password') }}
        </flux:button>
    </form>
</div>
