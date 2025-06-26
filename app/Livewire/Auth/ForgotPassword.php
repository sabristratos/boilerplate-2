<?php

namespace App\Livewire\Auth;

use App\Traits\WithToastNotifications;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    use WithToastNotifications;
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        $this->showSuccessToast(__('auth.forgot_password_link_sent'));
        session()->flash('status', __('auth.forgot_password_link_sent'));
    }
}
