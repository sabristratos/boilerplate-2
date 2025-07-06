<?php

declare(strict_types=1);

use App\Livewire\Admin\BlockLibrary;
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

describe('BlockLibrary Component', function () {
    it('can mount with page and block manager', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->assertSet('page.id', $this->page->id)
            ->assertSet('blockManager', $this->blockManager)
            ->assertSet('search', '')
            ->assertSet('selectedCategory', '');
    });

    it('displays all available block types when no filter is applied', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->assertSee('Content Area')
            ->assertSee('Call to Action')
            ->assertSee('Contact Form')
            ->assertSee('Hero Section')
            ->assertSee('Image Gallery')
            ->assertSee('Testimonials');
    });

    it('filters blocks by search term', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->set('search', 'content')
            ->assertSee('Content Area')
            ->assertDontSee('Call to Action')
            ->assertDontSee('Contact Form');
    });

    it('filters blocks by category', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->set('selectedCategory', 'content')
            ->assertSee('Content Area')
            ->assertDontSee('Call to Action')
            ->assertDontSee('Contact Form');
    });

    it('combines search and category filters', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->set('search', 'area')
            ->set('selectedCategory', 'content')
            ->assertSee('Content Area')
            ->assertDontSee('Call to Action');
    });

    it('can create a content area block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'content-area')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'content-area'
            ])
            ->assertDispatched('hide-block-library');

        // Verify the block was created in the database
        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'content-area'
        ]);
    });

    it('can create a call to action block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'call-to-action')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'call-to-action'
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'call-to-action'
        ]);
    });

    it('can create a contact form block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'contact')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'contact'
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'contact'
        ]);
    });

    it('can create a hero section block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'hero')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'hero'
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'hero'
        ]);
    });

    it('can create an image gallery block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'image-gallery')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'image-gallery'
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'image-gallery'
        ]);
    });

    it('can create a testimonials block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'testimonials')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'testimonials'
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'testimonials'
        ]);
    });

    it('can close the block library', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('close')
            ->assertDispatched('hide-block-library');
    });

    it('resets filters when close is called', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->set('search', 'test')
            ->set('selectedCategory', 'content')
            ->call('close')
            ->assertSet('search', '')
            ->assertSet('selectedCategory', '');
    });

    it('handles invalid block type gracefully', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->call('createBlock', 'invalid-block-type')
            ->assertHasErrors(['blockType']);

        $this->assertDatabaseMissing('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'invalid-block-type'
        ]);
    });

    it('displays block descriptions and icons', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager
            ])
            ->assertSee('Add rich text content')
            ->assertSee('Create compelling call-to-action sections')
            ->assertSee('Display contact forms')
            ->assertSee('Showcase hero sections with images and text')
            ->assertSee('Display image galleries')
            ->assertSee('Show customer testimonials');
    });
}); 