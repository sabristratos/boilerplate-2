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

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (): void {
    // Settings routes
    Route::redirect('settings', 'settings/general');
    Route::get('settings/{group}', \App\Livewire\SettingsPage::class)->name('settings.group');

    // Media Library routes
    Route::get('media', MediaLibrary::class)->name('media.index');
    Route::get('media/{id}', MediaDetail::class)->name('media.show');

    // Forms management
    Route::get('forms', FormIndex::class)->name('forms.index');
    Route::get('forms/{form}/edit', \App\Livewire\FormBuilder::class)->name('forms.edit');
    Route::get('forms/{form}/submissions', \App\Livewire\Admin\Forms\Submissions::class)->name('forms.submissions');
    Route::get('forms/{form}/submissions/{submission}', \App\Livewire\Admin\Forms\SubmissionDetails::class)->name('forms.submissions.show');

    // Translations management
    Route::get('translations', ManageTranslations::class)->name('translations.index');

    // Page management
    Route::get('pages', \App\Livewire\Admin\PageIndex::class)->name('pages.index');
    Route::get('pages/{page:id}/editor', \App\Livewire\Admin\PageManager::class)->name('pages.editor');

    // Analytics & Reports (placeholder routes for future implementation)
    Route::get('analytics', function () {
        return view('admin.analytics.index');
    })->name('analytics.index');

    Route::get('reports', function () {
        return view('admin.reports.index');
    })->name('reports.index');

    // Help & Documentation
    Route::get('help', function () {
        return view('admin.help.index');
    })->name('help.index');
});

require __DIR__.'/auth.php';

// Form display route
Route::get('/form/{form:id}', \App\Livewire\Frontend\FormDisplay::class)->name('forms.display');

// Dynamic page routes (must be last)
Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
