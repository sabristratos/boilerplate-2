<?php

declare(strict_types=1);

use App\Livewire\Admin\PageManager;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');

    $this->page = Page::factory()->create();
});

describe('PageManager Integration', function () {
    it('can create and manage blocks', function () {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->assertSee('Add Block')
            ->call('showBlockLibrary')
            ->assertSet('showBlockLibrary', true);
    });

    it('can create a hero block', function () {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('handleBlockCreated', ['blockId' => 1, 'blockType' => 'hero'])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'hero',
        ]);
    });

    it('can create a contact block', function () {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('handleBlockCreated', ['blockId' => 1, 'blockType' => 'contact'])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('content_blocks', [
            'page_id' => $this->page->id,
            'type' => 'contact',
        ]);
    });

    it('can edit blocks', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('editBlock', $block->id)
            ->assertDispatched('edit-block', ['blockId' => $block->id]);
    });

    it('can delete blocks', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('deleteBlock', $block->id)
            ->assertDispatched('block-deleted', ['blockId' => $block->id]);

        $this->assertDatabaseMissing('content_blocks', ['id' => $block->id]);
    });

    it('can duplicate blocks', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('duplicateBlock', $block->id)
            ->assertDispatched('block-created', ['blockId' => $block->id + 1, 'blockType' => 'hero']);

        $duplicatedBlock = ContentBlock::where('page_id', $this->page->id)
            ->where('type', 'hero')
            ->where('id', '!=', $block->id)
            ->first();

        expect($duplicatedBlock)->not->toBeNull();
        expect($duplicatedBlock->type)->toBe('hero');
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
            ->test(PageManager::class, ['page' => $this->page])
            ->call('updateBlockOrder', [$block2->id, $block1->id]);

        $this->assertDatabaseHas('content_blocks', [
            'id' => $block2->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('content_blocks', [
            'id' => $block1->id,
            'order' => 2,
        ]);
    });

    it('can save page', function () {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->set('page.title.en', 'Updated Title')
            ->call('savePage')
            ->assertDispatched('page-saved');

        $this->page->refresh();
        expect($this->page->title['en'])->toBe('Updated Title');
    });

    it('can publish page', function () {
        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('publishPage')
            ->assertDispatched('page-published');

        $this->page->refresh();
        expect($this->page->isPublished())->toBeTrue();
    });

    it('can unpublish page', function () {
        $this->page->update(['published_at' => now()]);

        Livewire::actingAs($this->user)
            ->test(PageManager::class, ['page' => $this->page])
            ->call('unpublishPage')
            ->assertDispatched('page-unpublished');

        $this->page->refresh();
        expect($this->page->isPublished())->toBeFalse();
    });
});
