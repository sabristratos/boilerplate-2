<?php

namespace Tests\Feature;

use App\Enums\ContentBlockStatus;
use App\Enums\PublishStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PageManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_edit_a_block_and_save_page()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit content');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'content-area',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.page-manager', ['page' => $page])
            ->call('editBlock', $block->id)
            ->assertSet('editingBlockId', $block->id)
            ->set('editingBlockState.content', 'New content for the block')
            ->set('editingBlockState.show_form', true)
            ->call('savePage')
            ->assertDispatched('$refresh');

        // Verify the block was updated
        $block->refresh();
        $this->assertEquals('New content for the block', $block->getTranslatedData()['content']);
        $this->assertTrue($block->getSettingsArray()['show_form']);
    }

    /** @test */
    public function it_can_cancel_block_editing()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit content');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'content-area',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.page-manager', ['page' => $page])
            ->call('editBlock', $block->id)
            ->assertSet('editingBlockId', $block->id)
            ->call('cancelBlockEdit')
            ->assertSet('editingBlockId', null)
            ->assertSet('editingBlockState', []);
    }

    /** @test */
    public function it_can_change_block_status()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit content');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'content-area',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.page-manager', ['page' => $page])
            ->call('editBlock', $block->id)
            ->assertSet('editingBlockStatus', ContentBlockStatus::DRAFT)
            ->call('changeBlockStatus', ['newStatus' => true])
            ->assertSet('editingBlockStatus', ContentBlockStatus::PUBLISHED)
            ->assertSet('editingBlockIsPublished', true);
    }

    /** @test */
    public function it_can_save_page_without_editing_block()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('pages.edit');

        $page = Page::factory()->create([
            'status' => PublishStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.page-manager', ['page' => $page])
            ->set('title.en', 'Updated Page Title')
            ->set('slug', 'updated-page-slug')
            ->call('savePage')
            ->assertDispatched('$refresh');

        $page->refresh();
        $this->assertEquals('Updated Page Title', $page->getTranslation('title', 'en'));
        $this->assertEquals('updated-page-slug', $page->slug);
    }

    /** @test */
    public function it_can_delete_a_block()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit content');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'content-area',
        ]);

        Livewire::actingAs($user)
            ->test('admin.page-manager', ['page' => $page])
            ->call('deleteBlock', $block->id)
            ->assertDispatched('$refresh');

        $this->assertDatabaseMissing('content_blocks', ['id' => $block->id]);
    }
} 