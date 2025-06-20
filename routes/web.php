<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\MediaLibrary;
use App\Livewire\MediaDetail;
use App\Livewire\Translations\ManageTranslations;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/general');

    // Dynamic routes for settings groups
    Route::get('settings/{group}', \App\Livewire\SettingsPage::class)
        ->name('settings.group');

    // Media Library routes
    Route::get('media', MediaLibrary::class)->name('media.index');
    Route::get('media/{id}', MediaDetail::class)->name('media.show');

    // Translations management
    Route::get('translations', ManageTranslations::class)->name('admin.translations.index');
});

require __DIR__.'/auth.php';
