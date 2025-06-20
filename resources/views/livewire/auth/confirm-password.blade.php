<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('auth.confirm_password_title')"
        :description="__('auth.confirm_password_description')"
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="confirmPassword" class="flex flex-col gap-6">
        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('labels.password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('labels.password')"
            viewable
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('buttons.confirm') }}</flux:button>
    </form>
</div>
