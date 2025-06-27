<?php

namespace Tests\Feature\Pages;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_a_page()
    {
        // Create a page
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        // Visit the page
        $response = $this->get('/' . $page->slug);

        $response->assertStatus(200);
        $response->assertSee('Test Page');
    }

    public function test_can_display_a_page_with_content_blocks()
    {
        // Create a page
        $page = Page::create([
            'title' => 'Page With Blocks',
            'slug' => 'page-with-blocks',
        ]);

        // Create a mock block class that extends the Block abstract class
        $mockBlockClass = new class extends \App\Blocks\Block {
            public function getName(): string
            {
                return 'Test Block';
            }

            public function getAdminView(): string
            {
                return 'admin.blocks._test-block';
            }

            public function getFrontendView(): string
            {
                return 'frontend.blocks._test-block';
            }

            public function getDefaultData(): array
            {
                return [];
            }

            public function validationRules(): array
            {
                return [];
            }
        };

        // Mock the BlockManager
        $this->mock(BlockManager::class, function ($mock) use ($mockBlockClass) {
            $mock->shouldReceive('find')
                ->with('test-block')
                ->andReturn($mockBlockClass);
        });

        // Create a content block
        ContentBlock::create([
            'type' => 'test-block',
            'page_id' => $page->id,
            'data' => ['content' => 'Test Block Content'],
            'status' => ContentBlockStatus::PUBLISHED,
        ]);

        // Create a view for the test block
        $this->withViewErrors([])
            ->blade('<div class="test-block">{{ $block->data["content"] }}</div>', [], 'frontend.blocks._test-block');

        // Visit the page
        $response = $this->get('/' . $page->slug);

        $response->assertStatus(200);
        $response->assertSee('Page With Blocks');
        $response->assertSee('Test Block Content');
    }

    public function test_can_display_a_page_in_different_languages()
    {
        // Create a page with translations
        $page = Page::create([
            'title' => 'English Page',
            'slug' => 'english-page',
        ]);

        // Add French translation
        $page->setTranslation('title', 'fr', 'Page Française');
        $page->setTranslation('slug', 'fr', 'page-francaise');
        $page->save();

        // Visit the page in English
        $response = $this->get('/' . $page->slug);
        $response->assertStatus(200);
        $response->assertSee('English Page');

        // Visit the page in French
        App::setLocale('fr');
        $response = $this->get('/page-francaise');
        $response->assertStatus(200);
        $response->assertSee('Page Française');

        // Reset locale
        App::setLocale('en');
    }

    public function test_returns_404_for_non_existent_page()
    {
        $response = $this->get('/non-existent-page');
        $response->assertStatus(404);
    }

    public function test_can_resolve_page_by_id()
    {
        // Create a page
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        // Visit the page using ID
        $response = $this->get('/' . $page->id);

        $response->assertStatus(200);
        $response->assertSee('Test Page');
    }

    public function test_can_display_content_blocks_in_order()
    {
        // Create a page
        $page = Page::create([
            'title' => 'Ordered Blocks Page',
            'slug' => 'ordered-blocks',
        ]);

        // Create a mock block class that extends the Block abstract class
        $mockBlockClass = new class extends \App\Blocks\Block {
            public function getName(): string
            {
                return 'Ordered Block';
            }

            public function getAdminView(): string
            {
                return 'admin.blocks._ordered-block';
            }

            public function getFrontendView(): string
            {
                return 'frontend.blocks._ordered-block';
            }

            public function getDefaultData(): array
            {
                return [];
            }

            public function validationRules(): array
            {
                return [];
            }
        };

        // Mock the BlockManager
        $this->mock(BlockManager::class, function ($mock) use ($mockBlockClass) {
            $mock->shouldReceive('find')
                ->with('ordered-block')
                ->andReturn($mockBlockClass);
        });

        // Create content blocks with specific order
        ContentBlock::create([
            'type' => 'ordered-block',
            'page_id' => $page->id,
            'data' => ['content' => 'First Block'],
            'status' => ContentBlockStatus::PUBLISHED,
            'order' => 2, // Set to display second
        ]);

        ContentBlock::create([
            'type' => 'ordered-block',
            'page_id' => $page->id,
            'data' => ['content' => 'Second Block'],
            'status' => ContentBlockStatus::PUBLISHED,
            'order' => 1, // Set to display first
        ]);

        ContentBlock::create([
            'type' => 'ordered-block',
            'page_id' => $page->id,
            'data' => ['content' => 'Third Block'],
            'status' => ContentBlockStatus::PUBLISHED,
            'order' => 3, // Set to display third
        ]);

        // Create a view for the ordered block
        $this->withViewErrors([])
            ->blade('<div class="ordered-block">{{ $block->data["content"] }}</div>', [], 'frontend.blocks._ordered-block');

        // Visit the page
        $response = $this->get('/' . $page->slug);

        $response->assertStatus(200);

        // Check that the blocks appear in the correct order in the HTML
        $responseContent = $response->getContent();
        $firstBlockPos = strpos($responseContent, 'Second Block'); // Order 1
        $secondBlockPos = strpos($responseContent, 'First Block'); // Order 2
        $thirdBlockPos = strpos($responseContent, 'Third Block'); // Order 3

        $this->assertNotFalse($firstBlockPos);
        $this->assertNotFalse($secondBlockPos);
        $this->assertNotFalse($thirdBlockPos);

        $this->assertLessThan($secondBlockPos, $firstBlockPos);
        $this->assertLessThan($thirdBlockPos, $secondBlockPos);
    }

    public function test_only_displays_published_content_blocks()
    {
        // Create a page
        $page = Page::create([
            'title' => 'Published Blocks Page',
            'slug' => 'published-blocks',
        ]);

        // Create a mock block class that extends the Block abstract class
        $mockBlockClass = new class extends \App\Blocks\Block {
            public function getName(): string
            {
                return 'Status Block';
            }

            public function getAdminView(): string
            {
                return 'admin.blocks._status-block';
            }

            public function getFrontendView(): string
            {
                return 'frontend.blocks._status-block';
            }

            public function getDefaultData(): array
            {
                return [];
            }

            public function validationRules(): array
            {
                return [];
            }
        };

        // Mock the BlockManager
        $this->mock(BlockManager::class, function ($mock) use ($mockBlockClass) {
            $mock->shouldReceive('find')
                ->with('status-block')
                ->andReturn($mockBlockClass);
        });

        // Create a published content block
        ContentBlock::create([
            'type' => 'status-block',
            'page_id' => $page->id,
            'data' => ['content' => 'Published Block'],
            'status' => ContentBlockStatus::PUBLISHED,
        ]);

        // Create a draft content block
        ContentBlock::create([
            'type' => 'status-block',
            'page_id' => $page->id,
            'data' => ['content' => 'Draft Block'],
            'status' => ContentBlockStatus::DRAFT,
        ]);

        // Create a view for the status block
        $this->withViewErrors([])
            ->blade('<div class="status-block">{{ $block->data["content"] }}</div>', [], 'frontend.blocks._status-block');

        // Visit the page
        $response = $this->get('/' . $page->slug);

        $response->assertStatus(200);
        $response->assertSee('Published Block');
        $response->assertDontSee('Draft Block');
    }
}
