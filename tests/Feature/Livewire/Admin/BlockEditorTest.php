<?php

declare(strict_types=1);

use App\Livewire\Admin\BlockEditor;
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

describe('BlockEditor', function () {
    it('renders the block editor with hero block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => [
                'heading' => 'Test Hero',
                'subheading' => 'Test Subheading'
            ],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->assertSee('Test Hero')
            ->assertSee('Test Subheading');
    });

    it('can edit hero block data', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => [
                'heading' => 'Original Heading',
                'subheading' => 'Original Subheading'
            ],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockState.heading', 'Updated Heading')
            ->set('editingBlockState.subheading', 'Updated Subheading')
            ->call('saveBlockDraft')
            ->assertDispatched('block-draft-saved', ['blockId' => $block->id]);
    });

    it('can save block draft', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockState.heading', 'Updated Heading')
            ->call('saveBlockDraft')
            ->assertDispatched('block-draft-saved', ['blockId' => $block->id]);

        // Verify the draft was saved
        $block->refresh();
        expect($block->getDraftData()['heading'])->toBe('Updated Heading');
    });

    it('can publish a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Draft Heading'],
            'draft_data' => ['heading' => 'Published Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->call('publishBlock')
            ->assertDispatched('block-published', ['blockId' => $block->id]);

        // Verify the block was published
        $block->refresh();
        expect($block->getTranslatedData()['heading'])->toBe('Published Heading');
    });

    it('can unpublish a block', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Published Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->call('unpublishBlock')
            ->assertDispatched('block-unpublished', ['blockId' => $block->id]);

        // Verify the block was unpublished
        $block->refresh();
        expect($block->isPublished())->toBeFalse();
    });

    it('can update block visibility', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockVisible', false)
            ->call('saveBlockDraft')
            ->assertDispatched('block-draft-saved', ['blockId' => $block->id]);

        // Verify the visibility was updated
        $block->refresh();
        expect($block->is_visible)->toBeFalse();
    });

    it('handles contact block editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'contact',
            'order' => 1,
            'data' => [
                'heading' => 'Contact Us',
                'subheading' => 'Get in touch'
            ],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->assertSee('Contact Us')
            ->assertSee('Get in touch');
    });

    it('can cancel editing', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockState.heading', 'Modified Heading')
            ->call('cancelEditing')
            ->assertSet('editingBlockId', null)
            ->assertSet('editingBlockState', []);
    });

    it('handles edit block events', function () {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'is_visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSet('editingBlockId', $block->id);
    });

    it('handles cancel block edit events', function () {
        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en'
            ])
            ->set('editingBlockId', 1)
            ->set('editingBlockState', ['heading' => 'Test'])
            ->dispatch('cancel-block-edit')
            ->assertSet('editingBlockId', null)
            ->assertSet('editingBlockState', []);
    });
}); 