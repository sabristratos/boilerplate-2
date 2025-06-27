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
    Route::get('admin/pages/{page}/editor', \App\Livewire\Admin\PageManager::class)->name('admin.pages.editor')->whereNumber('page');

    // Form Builder
    Route::get('admin/forms', \App\Livewire\Forms\FormIndex::class)->name('admin.forms.index')->middleware('can:view forms');
    Route::get('admin/forms/{form}/edit', \App\Livewire\Forms\FormBuilder::class)->name('admin.forms.edit')->middleware('can:edit forms');
    Route::get('admin/forms/{form}/submissions', \App\Livewire\Forms\SubmissionIndex::class)->name('admin.forms.submissions')->middleware('can:view form submissions');
});

require __DIR__.'/auth.php';

Route::get('/{page:slug}', [PageController::class, 'show'])
    ->where('page', '[a-zA-Z0-9_-]+')
    ->name('pages.show');
