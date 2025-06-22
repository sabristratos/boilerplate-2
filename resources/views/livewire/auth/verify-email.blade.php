<div class="space-y-6">
    <x-auth-header
        :title="__('auth.verify_email_title')"
        :description="__('auth.verify_email_description')"
    />

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-4" :status="__('auth.verification_link_sent')" />
    @endif

    <div class="flex flex-col items-center justify-between space-y-3">
        <flux:button
            wire:click="sendVerification"
            variant="primary"
            class="w-full"
        >
            {{ __('buttons.resend_verification_email') }}
        </flux:button>

        <flux:button
            wire:click="logout"
            variant="link"
        >
            {{ __('buttons.logout') }}
        </flux:button>
    </div>
</div>
