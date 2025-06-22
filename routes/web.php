<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\MediaLibrary;
use App\Livewire\MediaDetail;
use App\Livewire\Translations\ManageTranslations;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\PageIndex;
use App\Livewire\Admin\PageManager;
use App\Livewire\Admin\PageCreate;
use App\Livewire\Auth\Login;
use App\Livewire\SettingsPage;

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

    // Page Editor
    Route::get('admin/pages', \App\Livewire\Admin\PageIndex::class)->name('admin.pages.index');
    Route::get('admin/pages/create', \App\Livewire\Admin\PageCreate::class)->name('admin.pages.create');
    Route::get('admin/pages/{page}/editor', \App\Livewire\Admin\PageManager::class)->name('admin.pages.editor');
});

require __DIR__.'/auth.php';

Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
