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

// Sitemap routes
Route::get('sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (! file_exists($path)) {
        \Artisan::call('sitemap:generate');
    }

    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap.xml');

Route::get('sitemap.txt', function () {
    $path = public_path('sitemap.txt');
    if (! file_exists($path)) {
        \Artisan::call('sitemap:generate', ['--format' => 'txt']);
    }

    return response()->file($path, ['Content-Type' => 'text/plain']);
})->name('sitemap.txt');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
});

// Admin redirect route
Route::get('admin', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('admin');

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
    Route::get('forms/{id}/edit', \App\Livewire\FormBuilder::class)->name('forms.edit');
    Route::get('forms/{form}/submissions', \App\Livewire\Admin\Forms\Submissions::class)->name('forms.submissions');
    Route::get('forms/{form}/submissions/{submission}', \App\Livewire\Admin\Forms\SubmissionDetails::class)->name('forms.submissions.show');

    // Translations management
    Route::get('translations', ManageTranslations::class)->name('translations.index');

    // Page management
    Route::get('pages', \App\Livewire\Admin\PageIndex::class)->name('pages.index');
    Route::get('pages/{page:id}/editor', \App\Livewire\Admin\PageManager::class)->name('pages.editor');

    // Analytics & Reports (placeholder routes for future implementation)
    Route::get('analytics', fn() => view('admin.analytics.index'))->name('analytics.index');

    Route::get('reports', fn() => view('admin.reports.index'))->name('reports.index');

    // Help & Documentation
    Route::get('help', fn() => view('admin.help.index'))->name('help.index');

    // Database Backup
    Route::get('backup', \App\Livewire\Admin\DatabaseBackup::class)->name('backup.index');
    Route::get('backup/download/{filename}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');

    // Import/Export
    Route::get('import-export', \App\Livewire\Admin\ImportExport::class)->name('import-export.index');

    // Revisions
    Route::get('revisions/{modelType}/{modelId}', \App\Livewire\Admin\RevisionShow::class)->name('revisions.show');
});

require __DIR__.'/auth.php';

// Form display route
Route::get('/form/{form}', \App\Livewire\Frontend\FormDisplay::class)->name('forms.display');

// Debug route for testing
Route::get('/debug/form/{id}', function ($id): void {
    $form = \App\Models\Form::find($id);
    dd([
        'form_id' => $id,
        'form_found' => $form ? 'yes' : 'no',
        'form_data' => $form ? $form->toArray() : null,
        'elements' => $form ? $form->elements : null,
    ]);
});

// Dynamic page routes (must be last)
Route::get('/{page:slug}', [PageController::class, 'show'])
    ->where('page', '^(?!admin).*$')
    ->name('pages.show');
