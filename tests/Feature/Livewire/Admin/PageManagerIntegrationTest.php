<?php

declare(strict_types=1);

use App\Livewire\Admin\PageManager;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Models\User;
use App\Services\BlockManager;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function () {
    // Create all page permissions
    $permissions = [
        'pages.view',
        'pages.edit', 
        'pages.create',
        'pages.delete',
        'settings.general.manage'
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
    ]);

    // Now this will work because the user is authenticated and has permission
    Config::set('app.fallback_locale', 'en');
    app('settings')->set('general.available_locales', [
        ['code' => 'en', 'name' => 'English'],
        ['code' => 'fr', 'name' => 'French'],
    ]);
});

describe('PageManager Integration Tests', function () {
    it('can create and edit a block through the full workflow with translations', function () {
        $blockManager = app(BlockManager::class);

        // Test the complete workflow: create block -> edit block -> save changes
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSee('Test Page')
            ->assertSee('Content Area')
            ->assertSee('Call to Action');

        // Create a block with translations
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'Initial English content',
                    'fr' => 'Contenu français initial'
                ]
            ]
        ]);

        // Verify block appears in PageCanvas
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSee('Initial English content');

        // Edit the block with updated translations
        $block->update([
            'data' => [
                'content' => [
                    'en' => 'Updated English content',
                    'fr' => 'Contenu français mis à jour'
                ]
            ]
        ]);

        // Verify changes are reflected
        $block->refresh();
        expect($block->data['content']['en'])->toBe('Updated English content');
        expect($block->data['content']['fr'])->toBe('Contenu français mis à jour');
    });

    it('can reorder blocks and maintain order across locales', function () {
        $block1 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'sort' => 1,
            'data' => [
                'content' => [
                    'en' => 'First block',
                    'fr' => 'Premier bloc'
                ]
            ]
        ]);

        $block2 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'sort' => 2,
            'data' => [
                'content' => [
                    'en' => 'Second block',
                    'fr' => 'Deuxième bloc'
                ]
            ]
        ]);

        $block3 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'call-to-action',
            'sort' => 3,
            'data' => [
                'title' => [
                    'en' => 'Third block',
                    'fr' => 'Troisième bloc'
                ]
            ]
        ]);

        // Reorder blocks: 3, 1, 2
        $newOrder = [$block3->id, $block1->id, $block2->id];

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->dispatch('block-order-updated', ['sort' => $newOrder]);

        // Verify the order was updated
        $block1->refresh();
        $block2->refresh();
        $block3->refresh();

        expect($block1->sort)->toBe(2);
        expect($block2->sort)->toBe(3);
        expect($block3->sort)->toBe(1);
    });

    it('can create multiple blocks of different types with translations', function () {
        $blockManager = app(BlockManager::class);

        // Create different types of blocks with translations
        $contentBlock = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'Content block',
                    'fr' => 'Bloc de contenu'
                ]
            ]
        ]);

        $ctaBlock = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'call-to-action',
            'data' => [
                'title' => [
                    'en' => 'CTA Title',
                    'fr' => 'Titre CTA'
                ],
                'content' => [
                    'en' => 'CTA Content',
                    'fr' => 'Contenu CTA'
                ],
                'button_text' => [
                    'en' => 'Click Here',
                    'fr' => 'Cliquez Ici'
                ],
                'button_url' => 'https://example.com'
            ]
        ]);

        $contactBlock = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'contact',
            'data' => [
                'title' => [
                    'en' => 'Contact Us',
                    'fr' => 'Contactez-nous'
                ],
                'description' => [
                    'en' => 'Get in touch',
                    'fr' => 'Entrez en contact'
                ]
            ]
        ]);

        // Verify all blocks are displayed
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSee('Content block')
            ->assertSee('CTA Title')
            ->assertSee('Contact Us');
    });

    it('handles block deletion and updates the UI', function () {
        $block1 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'Block 1',
                    'fr' => 'Bloc 1'
                ]
            ]
        ]);

        $block2 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'Block 2',
                    'fr' => 'Bloc 2'
                ]
            ]
        ]);

        // Delete the first block
        $block1->delete();

        // Verify only the second block remains
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertDontSee('Block 1')
            ->assertSee('Block 2');

        $this->assertDatabaseMissing('content_blocks', ['id' => $block1->id]);
        $this->assertDatabaseHas('content_blocks', ['id' => $block2->id]);
    });

    it('can duplicate blocks and maintain data integrity with translations', function () {
        $originalBlock = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'call-to-action',
            'data' => [
                'title' => [
                    'en' => 'Original Title',
                    'fr' => 'Titre Original'
                ],
                'content' => [
                    'en' => 'Original Content',
                    'fr' => 'Contenu Original'
                ],
                'button_text' => [
                    'en' => 'Original Button',
                    'fr' => 'Bouton Original'
                ],
                'button_url' => 'https://original.com',
                'background_color' => 'bg-blue-500'
            ]
        ]);

        // Duplicate the block
        $duplicatedBlock = $originalBlock->replicate();
        $duplicatedBlock->save();

        // Verify the duplicated block has the same data
        expect($duplicatedBlock->type)->toBe('call-to-action');
        expect($duplicatedBlock->data['title']['en'])->toBe('Original Title');
        expect($duplicatedBlock->data['title']['fr'])->toBe('Titre Original');
        expect($duplicatedBlock->data['content']['en'])->toBe('Original Content');
        expect($duplicatedBlock->data['content']['fr'])->toBe('Contenu Original');
        expect($duplicatedBlock->data['button_text']['en'])->toBe('Original Button');
        expect($duplicatedBlock->data['button_text']['fr'])->toBe('Bouton Original');
        expect($duplicatedBlock->data['button_url'])->toBe('https://original.com');
        expect($duplicatedBlock->data['background_color'])->toBe('bg-blue-500');
    });

    it('can save page details and block changes independently with translations', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'Block content',
                    'fr' => 'Contenu du bloc'
                ]
            ]
        ]);

        // Save page details
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->set('title.en', 'Updated Page Title')
            ->set('title.fr', 'Titre de Page Mis à Jour')
            ->set('meta_title.en', 'Updated Meta Title')
            ->set('meta_title.fr', 'Titre Meta Mis à Jour')
            ->call('savePage');

        $this->page->refresh();
        expect($this->page->draft_title['en'])->toBe('Updated Page Title');
        expect($this->page->draft_title['fr'])->toBe('Titre de Page Mis à Jour');
        expect($this->page->draft_meta_title['en'])->toBe('Updated Meta Title');
        expect($this->page->draft_meta_title['fr'])->toBe('Titre Meta Mis à Jour');

        // Block data should remain unchanged
        $block->refresh();
        expect($block->data['content']['en'])->toBe('Block content');
        expect($block->data['content']['fr'])->toBe('Contenu du bloc');
    });

    it('handles locale switching with proper URL redirection', function () {
        $page = Page::factory()->create([
            'title' => ['en' => 'English Title', 'fr' => 'French Title'],
            'meta_title' => ['en' => 'English Meta', 'fr' => 'French Meta'],
        ]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $page])
            ->set('switchLocale', 'fr')
            ->assertRedirect(route('admin.pages.editor', ['page' => $page, 'locale' => 'fr']));
    });

    it('requires proper permissions for all operations', function () {
        $unauthorizedUser = User::factory()->create();
        
        // Test the policy directly
        $policy = new \App\Policies\PagePolicy();
        expect($policy->update($unauthorizedUser, $this->page))->toBeFalse();
        
        // Test that authorized user can access
        $authorizedUser = User::factory()->create();
        $authorizedUser->givePermissionTo('pages.edit');
        expect($policy->update($authorizedUser, $this->page))->toBeTrue();
    });

    it('handles draft and published content workflow', function () {
        // Create a page with draft content
        $page = Page::factory()->create([
            'title' => ['en' => 'Published Title'],
            'draft_title' => ['en' => 'Draft Title', 'fr' => 'Titre Brouillon'],
            'meta_title' => ['en' => 'Published Meta'],
            'draft_meta_title' => ['en' => 'Draft Meta', 'fr' => 'Meta Brouillon'],
        ]);

        // Verify draft content is loaded
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $page])
            ->assertSet('title.en', 'Draft Title')
            ->assertSet('title.fr', 'Titre Brouillon')
            ->assertSet('meta_title.en', 'Draft Meta')
            ->assertSet('meta_title.fr', 'Meta Brouillon');

        // Publish the draft
        $page->publishDraft();
        $page->refresh();

        // Verify published content is now loaded
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $page])
            ->assertSet('title.en', 'Draft Title')
            ->assertSet('title.fr', 'Titre Brouillon')
            ->assertSet('meta_title.en', 'Draft Meta')
            ->assertSet('meta_title.fr', 'Meta Brouillon');
    });

    it('handles missing translations gracefully in blocks', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => [
                    'en' => 'English content only'
                ]
            ]
        ]);

        // Verify the block loads without errors
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertHasNoErrors();

        // Verify French translation is null
        $block->refresh();
        expect($block->data['content']['fr'] ?? null)->toBeNull();
    });

    it('can handle complex nested data structures in blocks', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'call-to-action',
            'data' => [
                'title' => [
                    'en' => 'Main Title',
                    'fr' => 'Titre Principal'
                ],
                'content' => [
                    'en' => 'Main content with <strong>HTML</strong>',
                    'fr' => 'Contenu principal avec <strong>HTML</strong>'
                ],
                'button_text' => [
                    'en' => 'Click Here',
                    'fr' => 'Cliquez Ici'
                ],
                'button_url' => 'https://example.com',
                'background_color' => 'bg-blue-500',
                'text_color' => 'text-white',
                'alignment' => 'center'
            ]
        ]);

        // Verify complex data structure is handled correctly
        $block->refresh();
        expect($block->data['title']['en'])->toBe('Main Title');
        expect($block->data['title']['fr'])->toBe('Titre Principal');
        expect($block->data['content']['en'])->toBe('Main content with <strong>HTML</strong>');
        expect($block->data['content']['fr'])->toBe('Contenu principal avec <strong>HTML</strong>');
        expect($block->data['button_text']['en'])->toBe('Click Here');
        expect($block->data['button_text']['fr'])->toBe('Cliquez Ici');
        expect($block->data['button_url'])->toBe('https://example.com');
        expect($block->data['background_color'])->toBe('bg-blue-500');
        expect($block->data['text_color'])->toBe('text-white');
        expect($block->data['alignment'])->toBe('center');
    });
}); 