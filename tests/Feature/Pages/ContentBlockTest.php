<?php

namespace Tests\Feature\Pages;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ContentBlockTest extends TestCase
{
    use RefreshDatabase;

    protected Page $page;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test page for all tests
        $this->page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);
    }

    public function test_can_create_a_content_block()
    {
        $block = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'Test Heading'],
            'status' => ContentBlockStatus::PUBLISHED,
        ]);

        $this->assertDatabaseHas('content_blocks', [
            'id' => $block->id,
            'type' => 'hero-section',
            'page_id' => $this->page->id,
        ]);

        $this->assertEquals('New Hero Heading', $block->data['heading']);
        $this->assertEquals(ContentBlockStatus::PUBLISHED, $block->status);
    }

    public function test_can_update_a_content_block()
    {
        $block = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'Test Heading'],
            'status' => ContentBlockStatus::PUBLISHED,
        ]);

        $block->update([
            'data' => ['heading' => 'Updated Heading'],
            'status' => ContentBlockStatus::DRAFT,
        ]);

        $this->assertDatabaseHas('content_blocks', [
            'id' => $block->id,
            'type' => 'hero-section',
        ]);

        $block->refresh();
        $this->assertEquals('New Hero Heading', $block->data['heading']);
        $this->assertEquals(ContentBlockStatus::DRAFT, $block->status);
    }

    public function test_can_delete_a_content_block()
    {
        $block = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'Test Heading'],
        ]);

        $blockId = $block->id;
        $block->delete();

        $this->assertDatabaseMissing('content_blocks', [
            'id' => $blockId,
        ]);
    }

    public function test_handles_translations()
    {
        $block = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'English Heading', 'subheading' => 'English Subheading'],
        ]);

        // Add translations
        $block->setTranslation('data', 'fr', [
            'heading' => 'Titre Français',
            'subheading' => 'Sous-titre Français',
        ]);
        $block->save();

        // Check English (default)
        $this->assertEquals('New Hero Heading', $block->data['heading']);
        $this->assertEquals('Subheading text goes here.', $block->data['subheading']);

        // Check French
        App::setLocale('fr');
        $this->assertEquals('Titre Français', $block->data['heading']);
        $this->assertEquals('Sous-titre Français', $block->data['subheading']);

        // Reset locale
        App::setLocale('en');
    }

    public function test_merges_default_data_with_stored_data()
    {
        // Create a mock block class that extends the Block abstract class
        $mockBlockClass = new class extends \App\Blocks\Block {
            public function getName(): string
            {
                return 'Mock Block';
            }

            public function getAdminView(): string
            {
                return 'admin.blocks.mock';
            }

            public function getFrontendView(): string
            {
                return 'frontend.blocks.mock';
            }

            public function getDefaultData(): array
            {
                return [
                    'heading' => 'Default Heading',
                    'subheading' => 'Default Subheading',
                    'extra_field' => 'Default Extra',
                ];
            }

            public function validationRules(): array
            {
                return [
                    'heading' => 'required|string',
                    'subheading' => 'nullable|string',
                ];
            }
        };

        $mockBlockManager = $this->createMock(BlockManager::class);
        $mockBlockManager->method('find')
            ->with('mock-block')
            ->willReturn($mockBlockClass);

        $this->app->instance(BlockManager::class, $mockBlockManager);

        // Create a block with partial data
        $block = ContentBlock::create([
            'type' => 'mock-block',
            'page_id' => $this->page->id,
            'data' => [
                'heading' => 'Custom Heading',
                // Note: subheading and extra_field are not provided
            ],
        ]);

        // The data should include both the custom heading and the default values for missing fields
        $this->assertEquals('Default Heading', $block->data['heading']);
        $this->assertEquals('Default Subheading', $block->data['subheading']);
        $this->assertEquals('Default Extra', $block->data['extra_field']);
    }

    public function test_can_attach_media()
    {
        $block = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'Test Heading'],
        ]);

        // Create a test image
        $imagePath = storage_path('app/public/test-block-image.jpg');
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }
        file_put_contents($imagePath, 'test image content');

        // Add media to the block
        $media = $block->addMedia($imagePath)
            ->toMediaCollection('images');

        $this->assertCount(1, $block->getMedia('images'));
        $this->assertEquals('test-block-image.jpg', $block->getFirstMedia('images')->file_name);

        // Clean up
        @unlink($imagePath);
    }

    public function test_maintains_order_within_a_page()
    {
        // Create multiple blocks for the same page
        $block1 = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'First Block'],
        ]);

        $block2 = ContentBlock::create([
            'type' => 'content-area',
            'page_id' => $this->page->id,
            'data' => ['content' => 'Second Block'],
        ]);

        $block3 = ContentBlock::create([
            'type' => 'faq-section',
            'page_id' => $this->page->id,
            'data' => ['title' => 'Third Block'],
        ]);

        // Check that the order is set automatically
        $this->assertEquals(1, $block1->order);
        $this->assertEquals(2, $block2->order);
        $this->assertEquals(3, $block3->order);

        // Move the second block to the end
        $block2->moveToEnd();

        // Refresh from database
        $block1->refresh();
        $block2->refresh();
        $block3->refresh();

        // Check the new order
        $this->assertEquals(1, $block1->order);
        $this->assertEquals(3, $block2->order);
        $this->assertEquals(2, $block3->order);
    }

    public function test_belongs_to_a_page()
    {
        $block = ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $this->page->id,
            'data' => ['heading' => 'Test Heading'],
        ]);

        $this->assertInstanceOf(Page::class, $block->page);
        $this->assertEquals($this->page->id, $block->page->id);
    }
}
