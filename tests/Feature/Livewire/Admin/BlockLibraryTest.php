<?php

declare(strict_types=1);

use App\Livewire\Admin\BlockLibrary;
use App\Models\Page;
use App\Models\User;
use App\Services\BlockManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');

    $this->page = Page::factory()->create();
    $this->blockManager = app(BlockManager::class);
});

describe('BlockLibrary', function () {
    it('renders the block library with available blocks', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->assertSee('Contact Form')
            ->assertSee('Hero Section');
    });

    it('filters blocks by search term', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->set('search', 'hero')
            ->assertSee('Hero Section')
            ->assertDontSee('Contact Form');
    });

    it('filters blocks by category', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->set('selectedCategory', 'content')
            ->assertSee('Hero Section')
            ->assertDontSee('Contact Form');
    });

    it('combines search and category filters', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->set('search', 'hero')
            ->set('selectedCategory', 'content')
            ->assertSee('Hero Section')
            ->assertDontSee('Contact Form');
    });

    it('can create a contact form block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('createBlock', 'contact')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'contact',
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'contact',
        ]);
    });

    it('can create a hero section block', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('createBlock', 'hero')
            ->assertDispatched('block-created', [
                'blockId' => 1,
                'blockType' => 'hero',
            ]);

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'hero',
        ]);
    });

    it('can close the block library', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('close')
            ->assertDispatched('hide-block-library');
    });

    it('resets filters when close is called', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
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
                'blockManager' => $this->blockManager,
            ])
            ->call('createBlock', 'invalid-block-type')
            ->assertHasErrors(['blockType']);

        $this->assertDatabaseMissing('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'invalid-block-type',
        ]);
    });

    it('displays block descriptions and icons', function () {
        Livewire::actingAs($this->user)
            ->test(BlockLibrary::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->assertSee('Create compelling hero sections')
            ->assertSee('Display contact forms and information');
    });
});
