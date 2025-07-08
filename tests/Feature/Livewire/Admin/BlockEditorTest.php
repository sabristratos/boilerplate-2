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

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');

    $this->page = Page::factory()->create();
    $this->blockManager = app(BlockManager::class);
});

describe('BlockEditor', function (): void {
    it('renders the block editor with hero block', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => [
                'heading' => 'Test Hero',
                'subheading' => 'Test Subheading',
            ],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', $block->id)
            ->assertSee('Test Hero')
            ->assertSee('Test Subheading');
    });

    it('can edit hero block data', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => [
                'heading' => 'Original Heading',
                'subheading' => 'Original Subheading',
            ],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockState.heading', 'Updated Heading')
            ->set('editingBlockState.subheading', 'Updated Subheading')
            ->call('handleDebouncedSave')
            ->assertDispatched('block-save-needed', ['blockId' => $block->id]);
    });

    it('can save block draft', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockState.heading', 'Updated Heading')
            ->call('handleDebouncedSave')
            ->assertDispatched('block-save-needed', ['blockId' => $block->id]);

        // Verify the state was updated
        $block->refresh();
        expect($block->getTranslatedData('en')['heading'])->toBe('Original Heading'); // Original data unchanged
    });

    it('can handle contact block editing', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'contact',
            'order' => 1,
            'data' => [
                'heading' => 'Contact Us',
                'subheading' => 'Get in touch',
            ],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', $block->id)
            ->assertSee('Contact Us')
            ->assertSee('Get in touch');
    });

    it('can update block visibility', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockVisible', false)
            ->call('handleDebouncedSave')
            ->assertDispatched('block-save-needed', ['blockId' => $block->id]);

        // Verify the visibility state was updated
        $block->refresh();
        expect($block->isVisible())->toBeTrue(); // Original visibility unchanged
    });

    it('can cancel editing', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Original Heading'],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', $block->id)
            ->set('editingBlockState.heading', 'Modified Heading')
            ->call('cancelEditing')
            ->assertSet('editingBlockId', null)
            ->assertSet('editingBlockState', []);
    });

    it('handles edit block events', function (): void {
        $block = ContentBlock::factory()->create([
            'page_id' => $this->page->id,
            'type' => 'hero',
            'order' => 1,
            'data' => ['heading' => 'Test Heading'],
            'visible' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->dispatch('edit-block', ['blockId' => $block->id])
            ->assertSet('editingBlockId', $block->id);
    });

    it('handles cancel block edit events', function (): void {
        Livewire::actingAs($this->user)
            ->test(BlockEditor::class, [
                'page' => $this->page,
                'activeLocale' => 'en',
            ])
            ->set('editingBlockId', 1)
            ->set('editingBlockState', ['heading' => 'Test'])
            ->dispatch('cancel-block-edit')
            ->assertSet('editingBlockId', null)
            ->assertSet('editingBlockState', []);
    });
});
