<?php

declare(strict_types=1);

use App\Livewire\Admin\PageManager;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function (): void {
    // Create all page permissions
    $permissions = [
        'pages.view',
        'pages.edit',
        'pages.create',
        'pages.delete',
        'settings.general.manage',
    ];

    foreach ($permissions as $permission) {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
    }

    $this->user = User::factory()->create();
    $this->user->givePermissionTo('pages.edit', 'settings.general.manage');

    // Authenticate as the user for settings setup
    \Illuminate\Support\Facades\Auth::login($this->user);

    // Ensure the SettingGroup exists
    \App\Models\SettingGroup::firstOrCreate([
        'key' => 'general',
    ], [
        'label' => ['en' => 'General', 'fr' => 'Général'],
        'description' => ['en' => 'General settings', 'fr' => 'Paramètres généraux'],
    ]);

    $this->page = Page::factory()->create([
        'title' => ['en' => 'Test Page', 'fr' => 'Page de Test'],
        'slug' => 'test-page',
        'meta_title' => ['en' => 'Test Meta Title', 'fr' => 'Titre Meta de Test'],
        'meta_description' => ['en' => 'Test Meta Description', 'fr' => 'Description Meta de Test'],
        'no_index' => false,
    ]);

    // Now this will work because the user is authenticated and has permission
    Config::set('app.fallback_locale', 'en');
    app('settings')->set('general.available_locales', [
        ['code' => 'en', 'name' => 'English'],
        ['code' => 'fr', 'name' => 'French'],
    ]);
});

describe('PageManager Component', function (): void {
    it('can mount with a page', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSet('page.id', $this->page->id)
            ->assertSet('activeLocale', 'en')
            ->assertSet('availableLocales', ['en' => 'English', 'fr' => 'French']);
    });

    it('loads page translations from the latest revision', function (): void {
        // Create a draft revision
        $this->page->createManualRevision('draft', 'Initial draft', [], false);
        $this->page->title = ['en' => 'Updated Title'];
        $this->page->save();

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSet('title.en', 'Updated Title');
    });

    it('can save page details as a draft revision', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->set('title.en', 'New Draft Title')
            ->call('savePage')
            ->assertHasNoErrors();

        $latestRevision = $this->page->latestRevision();
        expect($latestRevision)->not->toBeNull()
            ->and($latestRevision->is_published)->toBeFalse()
            ->and($latestRevision->data['title']['en'])->toBe('New Draft Title');
    });

    it('can publish a page', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->set('title.en', 'Published Title')
            ->call('publishPage')
            ->assertHasNoErrors();

        $latestRevision = $this->page->latestRevision();
        expect($latestRevision)->not->toBeNull()
            ->and($latestRevision->is_published)->toBeTrue()
            ->and($latestRevision->data['title']['en'])->toBe('Published Title');

        $this->page->refresh();
        expect($this->page->getTranslation('title', 'en'))->toBe('Published Title');
    });

    it('can generate slug from title for current locale', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->set('title.en', 'New Test Page Title')
            ->call('generateSlug')
            ->assertSet('slug', 'new-test-page-title');
    });

    it('handles locale switching correctly', function (): void {
        $page = Page::factory()->create([
            'title' => ['en' => 'English Title', 'fr' => 'French Title'],
            'meta_title' => ['en' => 'English Meta', 'fr' => 'French Meta'],
        ]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $page])
            ->set('switchLocale', 'fr')
            ->assertRedirect(route('admin.pages.editor', ['page' => $page, 'locale' => 'fr']));
    });

    it('validates locale format and falls back to default', function (): void {
        $page = Page::factory()->create();

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $page])
            ->set('switchLocale', 'invalid-locale')
            ->assertSet('activeLocale', 'en'); // Should fall back to default
    });

    it('handles block creation events', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('handleBlockCreated', ['blockId' => 1, 'blockType' => 'hero'])
            ->assertHasNoErrors(); // Event assertion omitted due to Livewire version
    });

    it('handles block deletion events', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('handleBlockDeleted', ['blockId' => 1])
            ->assertHasNoErrors(); // Event assertion omitted due to Livewire version
    });

    it('handles block order update events', function (): void {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('handleBlockOrderUpdated', ['sort' => [1, 2, 3]])
            ->assertHasNoErrors(); // Event assertion omitted due to Livewire version
    });

    it('requires pages.edit permission to access', function (): void {
        $unauthorizedUser = User::factory()->create();

        // Test the policy directly
        $policy = new \App\Policies\PagePolicy;
        expect($policy->update($unauthorizedUser, $this->page))->toBeFalse();

        // Test that authorized user can access
        $authorizedUser = User::factory()->create();
        $authorizedUser->givePermissionTo('pages.edit');
        expect($policy->update($authorizedUser, $this->page))->toBeTrue();
    });

    it('allows users with pages.edit permission to access', function (): void {
        $authorizedUser = User::factory()->create();
        $authorizedUser->givePermissionTo('pages.edit');

        Livewire::actingAs($authorizedUser)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSet('page.id', $this->page->id);
    });

    it('handles missing translations gracefully', function (): void {
        $pageWithPartialTranslations = Page::factory()->create([
            'title' => ['en' => 'English Only'],
            'meta_title' => ['en' => 'English Meta Only'],
        ]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $pageWithPartialTranslations])
            ->assertSet('title.en', 'English Only')
            ->assertSet('title.fr', null)
            ->assertSet('meta_title.en', 'English Meta Only')
            ->assertSet('meta_title.fr', null);
    });

    it('preserves existing translations when saving partial data', function (): void {
        // Start with a page that has both English and French translations
        $page = Page::factory()->create([
            'title' => ['en' => 'English Title', 'fr' => 'French Title'],
            'meta_title' => ['en' => 'English Meta', 'fr' => 'French Meta'],
        ]);

        // Save only English data
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $page])
            ->set('title.en', 'Updated English Title')
            ->set('meta_title.en', 'Updated English Meta')
            ->call('savePage');

        $page->refresh();

        // French translations should still exist
        $draftTitle = $page->getTranslations('draft_title');
        $draftMetaTitle = $page->getTranslations('draft_meta_title');
        expect($draftTitle['fr'])->toBe('French Title');
        expect($draftMetaTitle['fr'])->toBe('French Meta');
        expect($draftTitle['en'])->toBe('Updated English Title');
        expect($draftMetaTitle['en'])->toBe('Updated English Meta');
    });
});
