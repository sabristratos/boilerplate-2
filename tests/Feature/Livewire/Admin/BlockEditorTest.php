<?php

declare(strict_types=1);

use App\Livewire\Admin\BlockEditor;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Models\User;
use App\Services\BlockManager;
use Livewire\Livewire;

beforeEach(function () {
    // Create the permission if it doesn't exist
    $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'pages.edit']);
    
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('pages.edit');
    
    $this->page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
    ]);

    $this->blockManager = app(BlockManager::class);
});

describe('BlockEditor Component', function () {
    it('can mount with page and block manager', function () {
        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->assertSet('page.id', $this->page->id)
            ->assertSet('activeLocale', 'en')
            ->assertSet('editingBlockId', null);
    });

    it('shows editor when edit-block event is received', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Test content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSet('editingBlockId', $block->id);
    });

    it('loads block data when editing starts', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => [
                'content' => ['en' => 'Original content'],
                'background_color' => 'bg-blue-500',
                'text_color' => 'text-white'
            ]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSet('blockData.content.en', 'Original content')
            ->assertSet('blockData.background_color', 'bg-blue-500')
            ->assertSet('blockData.text_color', 'text-white');
    });

    it('can save block changes', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Original content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->set('blockData.content.en', 'Updated content')
            ->set('blockData.background_color', 'bg-red-500')
            ->call('saveBlock')
            ->assertDispatched('block-saved', ['blockId' => $block->id]);

        $block->refresh();
        expect($block->data['content']['en'])->toBe('Updated content');
        expect($block->data['background_color'])->toBe('bg-red-500');
    });

    it('can close the editor', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area'
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->call('close')
            ->assertSet('editingBlockId', null);
    });

    it('auto-saves changes after a delay', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Original content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->set('blockData.content.en', 'Auto-saved content')
            ->call('autoSave')
            ->assertDispatched('block-saved', ['blockId' => $block->id]);

        $block->refresh();
        expect($block->data['content']['en'])->toBe('Auto-saved content');
    });

    it('handles content area block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Test content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSee('Content Area')
            ->assertSee('Content')
            ->assertSee('Background Color')
            ->assertSee('Text Color');
    });

    it('handles call to action block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'call-to-action',
            'data' => [
                'title' => ['en' => 'CTA Title'],
                'content' => ['en' => 'CTA Content'],
                'button_text' => ['en' => 'Click Here'],
                'button_url' => 'https://example.com'
            ]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSee('Call to Action')
            ->assertSee('Title')
            ->assertSee('Content')
            ->assertSee('Button Text')
            ->assertSee('Button URL');
    });

    it('handles contact form block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'contact',
            'data' => [
                'title' => ['en' => 'Contact Us'],
                'description' => ['en' => 'Get in touch'],
                'form_id' => 1
            ]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSee('Contact Form')
            ->assertSee('Title')
            ->assertSee('Description')
            ->assertSee('Form');
    });

    it('handles hero section block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'data' => [
                'title' => ['en' => 'Hero Title'],
                'subtitle' => ['en' => 'Hero Subtitle'],
                'background_image' => 'hero-bg.jpg'
            ]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSee('Hero Section')
            ->assertSee('Title')
            ->assertSee('Subtitle')
            ->assertSee('Background Image');
    });

    it('handles image gallery block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'image-gallery',
            'data' => [
                'title' => ['en' => 'Gallery Title'],
                'images' => ['image1.jpg', 'image2.jpg']
            ]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSee('Image Gallery')
            ->assertSee('Title')
            ->assertSee('Images');
    });

    it('handles testimonials block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'testimonials',
            'data' => [
                'title' => ['en' => 'Testimonials'],
                'testimonials' => [
                    ['name' => 'John Doe', 'content' => 'Great service!'],
                    ['name' => 'Jane Smith', 'content' => 'Amazing!']
                ]
            ]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSee('Testimonials')
            ->assertSee('Title')
            ->assertSee('Testimonials List');
    });

    it('validates required fields before saving', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Test content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->set('blockData.content.en', '') // Empty content
            ->call('saveBlock')
            ->assertHasErrors(['blockData.content.en']);
    });

    it('handles non-existent block gracefully', function () {
        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => 99999])
            ->assertSet('editingBlockId', null)
            ->assertSet('isVisible', false);
    });

    it('resets form data when closing editor', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Original content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->set('blockData.content.en', 'Modified content')
            ->call('close')
            ->assertSet('blockData', [])
            ->assertSet('editingBlockId', null);
    });
}); 