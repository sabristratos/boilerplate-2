<?php

declare(strict_types=1);

use App\Livewire\Admin\PageCanvas;
use App\Models\ContentBlock;
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

describe('PageCanvas', function () {
    it('renders the page canvas with blocks', function () {
        // Create some test blocks
        ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Hero'],
            'is_visible' => true,
        ]);

        ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'contact',
            'order' => 2,
            'data' => ['heading' => 'Test Contact'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->assertSee('Test Hero')
            ->assertSee('Test Contact');
    });

    it('can edit a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('editBlock', $block->id)
            ->assertDispatched('edit-block', ['blockId' => $block->id]);
    });

    it('can duplicate a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('duplicateBlock', $block->id)
            ->assertDispatched('block-created', ['blockId' => $block->id + 1, 'blockType' => 'hero']);

        // Verify the duplicated block exists
        $duplicatedBlock = ContentBlock::where('page_id', $this->page->id)
            ->where('type', 'hero')
            ->where('id', '!=', $block->id)
            ->first();

        expect($duplicatedBlock)->not->toBeNull();
        expect($duplicatedBlock->type)->toBe('hero');
    });

    it('can delete a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('deleteBlock', $block->id)
            ->assertDispatched('block-deleted', ['blockId' => $block->id]);

        $this->assertDatabaseMissing('content_blocks', ['id' => $block->id]);
    });

    it('can update block order', function () {
        $block1 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'is_visible' => true,
        ]);

        $block2 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'contact',
            'order' => 2,
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->call('updateBlockOrder', [$block2->id, $block1->id]);

        // Verify the order was updated
        $this->assertDatabaseHas('content_blocks', [
            'id' => $block2->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('content_blocks', [
            'id' => $block1->id,
            'order' => 2,
        ]);
    });

    it('handles block editing events', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->dispatch('block-editing-started', [
                'blockId' => $block->id,
                'blockState' => ['heading' => 'Updated Heading'],
            ])
            ->assertSet('editingBlockId', $block->id)
            ->assertSet('editingBlockState', ['heading' => 'Updated Heading']);
    });

    it('handles block editing cancelled events', function () {
        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'blockManager' => $this->blockManager,
            ])
            ->set('editingBlockId', 1)
            ->set('editingBlockState', ['heading' => 'Test'])
            ->dispatch('block-editing-cancelled')
            ->assertSet('editingBlockId', null)
            ->assertSet('editingBlockState', []);
    });
});
