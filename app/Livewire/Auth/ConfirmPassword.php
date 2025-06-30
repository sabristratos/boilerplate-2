<?php

namespace App\Livewire\Auth;

use App\Traits\WithToastNotifications;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ConfirmPassword extends Component
{
    use WithToastNotifications;

    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            $this->showErrorToast(__('auth.password'));

            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->showSuccessToast(__('auth.password_confirmed'));

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}
