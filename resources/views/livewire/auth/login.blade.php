<div class="space-y-6">
    <x-auth-header
        :title="__('auth.login_title')"
        :description="__('auth.login_description')"
    />

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6 p-2">
        <flux:input
            wire:model="email"
            type="email"
            :label="__('labels.email')"
            required
            autofocus
        />

        <flux:input
            wire:model="password"
            type="password"
            :label="__('labels.password')"
            required
            viewable
        />

        <div class="flex items-center justify-between">
            <flux:checkbox
                id="remember_me"
                wire:model="remember"
                :label="__('labels.remember_me')"
            />

            <flux:link
                href="{{ route('password.request') }}"
                variant="subtle"
                class="text-xs"
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

    @if(count($this->enabledProviders) > 0)
        <flux:separator text="{{ __('auth.or') }}" />
        
        <div class="space-y-3">
            @foreach($this->enabledProviders as $provider)
                <flux:button
                    wire:click="socialLogin('{{ $provider }}')"
                    variant="outline"
                    class="w-full"
                    icon="{{ $provider }}"
                >
                    {{ __('auth.login_with', ['provider' => ucfirst($provider)]) }}
                </flux:button>
            @endforeach
        </div>
    @endif

    @if(setting('security.enable_registration'))
        <div class="text-center">
            <flux:text size="sm">
                {{ __('auth.register_prompt') }}
                <flux:link href="{{ route('register') }}">
                    {{ __('buttons.create_account') }}
                </flux:link>
            </flux:text>
        </div>
    @endif

</div>
