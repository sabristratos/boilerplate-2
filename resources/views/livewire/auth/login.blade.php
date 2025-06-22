<div class="space-y-6">
    <x-auth-header
        :title="__('auth.login_title')"
        :description="__('auth.login_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6 p-2">
        <flux:field :label="__('labels.email')">
            <flux:input
                wire:model="email"
                type="email"
                required
                autofocus
            />
        </flux:field>

        <flux:field :label="__('labels.password')">
            <flux:input
                wire:model="password"
                type="password"
                required
            />
        </flux:field>

        <div class="flex items-center justify-between">
            <flux:field variant="inline">
                <flux:label for="remember_me">{{ __('labels.remember_me') }}</flux:label>
                <flux:checkbox id="remember_me" wire:model="remember" />
            </flux:field>

            <flux:link
                href="{{ route('password.request') }}"
            >
                {{ __('auth.forgot_password_title') }}?
            </flux:link>
        </div>


        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
        >
            {{ __('buttons.login') }}
        </flux:button>
    </form>

    @if(setting('security.enable_registration'))
        <div class="text-center">
            <flux:text href="{{ route('register') }}"
                       variant="link" size="sm">
                {{ __('auth.register_prompt') }}
            </flux:text>
        </div>
    @endif

</div>
