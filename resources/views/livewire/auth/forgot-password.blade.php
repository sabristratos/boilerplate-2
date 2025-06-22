<div class="space-y-6">
    <x-auth-header
        :title="__('auth.forgot_password_title')"
        :description="__('auth.forgot_password_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-6">
        <flux:field :label="__('labels.email')">
            <flux:input
                wire:model="email"
                type="email"
                required
                autofocus
            />
        </flux:field>

        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
        >
            {{ __('buttons.email_password_reset_link') }}
        </flux:button>
    </form>

    <div class="text-center">
        <flux:text size="sm">
            {{ __('auth.return_to_login') }}
            <flux:link
                href="{{ route('login') }}"
            >
                {{ __('buttons.login') }}
            </flux:link>
        </flux:text>
    </div>
</div>
