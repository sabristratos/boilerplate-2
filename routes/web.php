<?php

use App\Facades\Settings;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Livewire\Admin\Forms\Index as FormIndex;
use App\Livewire\Dashboard;
use App\Livewire\MediaDetail;
use App\Livewire\MediaLibrary;
use App\Livewire\Translations\ManageTranslations;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::redirect('settings', 'settings/general');

    // Dynamic routes for settings groups
    Route::get('settings/{group}', \App\Livewire\SettingsPage::class)
        ->name('settings.group');

    // Media Library routes
    Route::get('media', MediaLibrary::class)->name('media.index');
    Route::get('media/{id}', MediaDetail::class)->name('media.show');

    Route::get('/forms', FormIndex::class)->name('admin.forms.index');
    Route::get('/forms/{form}/edit', \App\Livewire\FormBuilder::class)->name('admin.forms.edit');
    Route::get('/forms/{form}/submissions', \App\Livewire\Admin\Forms\Submissions::class)->name('admin.forms.submissions');

    // Translations management
    Route::get('translations', ManageTranslations::class)->name('admin.translations.index');

    // Page Editor
    Route::get('admin/pages', \App\Livewire\Admin\PageIndex::class)->name('admin.pages.index');
    Route::get('admin/pages/{page:id}/editor', \App\Livewire\Admin\PageManager::class)->name('admin.pages.editor');
});

require __DIR__.'/auth.php';

Route::get('/{page:slug}', [PageController::class, 'show'])
    ->name('pages.show');
