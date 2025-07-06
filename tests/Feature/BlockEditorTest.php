<?php

namespace Tests\Feature;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Models\User;
use App\Services\BlockManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BlockEditorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_edit_contact_block()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('content-blocks.edit');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'contact',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.block-editor')
            ->call('startEditing', $block->id)
            ->assertSet('editingBlock.id', $block->id)
            ->set('state.heading', 'New Contact Heading')
            ->set('state.background_color', 'blue')
            ->set('state.show_contact_info', false)
            ->call('saveBlock')
            ->assertNotDispatched('blockEditCancelled'); // Should not close the panel
    }

    /** @test */
    public function it_can_edit_features_block()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('content-blocks.edit');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'features',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.block-editor')
            ->call('startEditing', $block->id)
            ->assertSet('editingBlock.id', $block->id)
            ->set('state.heading', 'New Features Heading')
            ->set('state.columns', 2)
            ->set('state.layout', 'list')
            ->call('saveBlock')
            ->assertNotDispatched('blockEditCancelled'); // Should not close the panel
    }

    /** @test */
    public function it_can_edit_testimonials_block()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('content-blocks.edit');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'testimonials',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.block-editor')
            ->call('startEditing', $block->id)
            ->assertSet('editingBlock.id', $block->id)
            ->set('state.heading', 'New Testimonials Heading')
            ->set('state.show_avatars', false)
            ->set('state.show_ratings', false)
            ->call('saveBlock')
            ->assertNotDispatched('blockEditCancelled'); // Should not close the panel
    }

    /** @test */
    public function it_can_edit_call_to_action_block()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('content-blocks.edit');

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'call-to-action',
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Livewire::actingAs($user)
            ->test('admin.block-editor')
            ->call('startEditing', $block->id)
            ->assertSet('editingBlock.id', $block->id)
            ->set('state.heading', 'New CTA Heading')
            ->set('state.background_color', 'green')
            ->set('state.text_alignment', 'right')
            ->call('saveBlock')
            ->assertNotDispatched('blockEditCancelled'); // Should not close the panel
    }
} 