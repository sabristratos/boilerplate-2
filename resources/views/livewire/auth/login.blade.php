<x-layouts.auth :title="__('auth.login_title')">
    <div class="space-y-6">
        <x-auth-header
            :title="__('auth.login_title')"
            :description="__('auth.login_description')"
        />

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-6">
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

                <flux:button
                    href="{{ route('password.request') }}"
                    variant="primary"
                >
                    {{ __('auth.forgot_password_title') }}?
                </flux:button>
            </div>


            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
            >
                {{ __('buttons.login') }}
            </flux:button>
        </form>

        <div class="text-center">
            <flux:text href="{{ route('register') }}"
                       variant="link" size="sm">
                {{ __('auth.register_prompt') }}
            </flux:text>
        </div>
    </div>
</x-layouts.auth>
