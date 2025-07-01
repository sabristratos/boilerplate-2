<?php

namespace App\Livewire;

use App\Facades\Settings;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    public array $locales = [];

    public string $currentLocale;

    public function mount(): void
    {
        $this->locales = Settings::get('general.available_locales', []);
        $this->currentLocale = app()->getLocale() ?? 'en';
    }

    public function updatedCurrentLocale($locale)
    {
        if (in_array($locale, array_column($this->locales, 'code'))) {
            Session::put('locale', $locale);

            if (auth()->check()) {
                auth()->user()->update(['locale' => $locale]);
            }

            return redirect(request()->header('Referer'));
        }

        return null;
    }

    public function render()
    {
        return view('livewire.locale-switcher');
    }
}
