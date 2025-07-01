<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{
    public function __construct(
        protected SocialLoginService $socialLoginService
    ) {}

    /**
     * Redirect the user to the OAuth provider
     */
    public function redirect(string $provider)
    {
        try {
            return $this->socialLoginService->redirect($provider);
        } catch (\Exception $e) {
            Log::error('Social login redirect failed in controller', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('login')
                ->withErrors(['email' => 'Unable to connect to social login provider. Please try again.']);
        }
    }

    /**
     * Handle the OAuth callback
     */
    public function callback(string $provider)
    {
        try {
            return $this->socialLoginService->handleCallback($provider);
        } catch (\Exception $e) {
            Log::error('Social login callback failed in controller', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('login')
                ->withErrors(['email' => 'Social login failed. Please try again or use your email and password.']);
        }
    }
} 