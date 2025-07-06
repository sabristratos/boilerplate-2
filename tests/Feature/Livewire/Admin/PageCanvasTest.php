<?php

declare(strict_types=1);

use App\Livewire\Admin\PageCanvas;
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

describe('PageCanvas Component', function () {
    it('can mount with page and block manager', function () {
        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->assertSet('page.id', $this->page->id)
            ->assertSet('activeLocale', 'en');
    });

    it('displays blocks in correct order', function () {
        $block1 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'sort' => 1,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'First block']]
        ]);

        $block2 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'sort' => 2,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Second block']]
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->assertSee('First block')
            ->assertSee('Second block');
    });

    it('can initiate block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Test content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->call('editBlock', $block->id)
            ->assertDispatched('edit-block', ['blockId' => $block->id]);
    });

    it('can delete a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area'
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->call('deleteBlock', $block->id)
            ->assertDispatched('block-deleted', ['blockId' => $block->id]);

        $this->assertDatabaseMissing('content_blocks', ['id' => $block->id]);
    });

    it('can reorder blocks', function () {
        $block1 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'sort' => 1,
            'type' => 'content-area'
        ]);

        $block2 = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'sort' => 2,
            'type' => 'content-area'
        ]);

        $newOrder = [$block2->id, $block1->id];

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->call('updateBlockOrder', $newOrder)
            ->assertDispatched('block-order-updated', ['sort' => $newOrder]);

        // Verify the order was updated in the database
        $block1->refresh();
        $block2->refresh();
        expect($block1->sort)->toBe(2);
        expect($block2->sort)->toBe(1);
    });

    it('handles empty page with no blocks', function () {
        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->assertSee(__('messages.page_canvas.no_blocks_message'));
    });

    it('dispatches block creation event when add block is clicked', function () {
        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->call('addBlock')
            ->assertDispatched('show-block-library');
    });

    it('can duplicate a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area',
            'data' => ['content' => ['en' => 'Original content']]
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->call('duplicateBlock', $block->id)
            ->assertDispatched('block-created', ['blockId' => $block->id + 1, 'blockType' => 'content-area']);

        // Verify the block was duplicated
        $duplicatedBlock = ContentBlock::where('page_id', $this->page->id)
            ->where('id', '!=', $block->id)
            ->first();
        
        expect($duplicatedBlock)->not->toBeNull();
        expect($duplicatedBlock->type)->toBe('content-area');
        expect($duplicatedBlock->data['content']['en'])->toBe('Original content');
    });

    it('handles block editing with different block types', function () {
        $contentBlock = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'content-area'
        ]);

        $ctaBlock = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'call-to-action'
        ]);

        Livewire::actingAs($this->user)
            ->test(PageCanvas::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->call('editBlock', $contentBlock->id)
            ->assertDispatched('edit-block', ['blockId' => $contentBlock->id])
            ->call('editBlock', $ctaBlock->id)
            ->assertDispatched('edit-block', ['blockId' => $ctaBlock->id]);
    });
}); 