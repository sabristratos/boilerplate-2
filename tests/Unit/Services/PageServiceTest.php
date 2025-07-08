<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\PageDTO;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageService = app(PageService::class);
    }

    /** @test */
    public function it_can_create_a_page_from_dto(): void
    {
        $pageData = [
            'title' => ['en' => 'Test Page', 'fr' => 'Page de Test'],
            'slug' => 'test-page',
            'meta_title' => ['en' => 'Test Meta Title', 'fr' => 'Titre Meta de Test'],
            'meta_description' => ['en' => 'Test meta description', 'fr' => 'Description meta de test'],
            'no_index' => false,
        ];

        $pageDTO = PageDTO::fromArray($pageData);
        $page = $this->pageService->createPage($pageDTO);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('test-page', $page->slug);
        $this->assertEquals(['en' => 'Test Page', 'fr' => 'Page de Test'], $page->getTranslations('title'));
        $this->assertEquals(['en' => 'Test Meta Title', 'fr' => 'Titre Meta de Test'], $page->getTranslations('meta_title'));
        $this->assertEquals(['en' => 'Test meta description', 'fr' => 'Description meta de test'], $page->getTranslations('meta_description'));
        $this->assertFalse($page->no_index);
    }

    /** @test */
    public function it_can_update_a_page_from_dto(): void
    {
        $page = Page::factory()->create([
            'title' => ['en' => 'Original Title'],
            'slug' => 'original-slug',
        ]);

        $pageData = [
            'id' => $page->id,
            'title' => ['en' => 'Updated Title', 'fr' => 'Titre Mis à Jour'],
            'slug' => 'updated-slug',
            'meta_title' => ['en' => 'Updated Meta Title'],
            'meta_description' => ['en' => 'Updated meta description'],
            'no_index' => true,
        ];

        $pageDTO = PageDTO::fromArray($pageData);
        $updatedPage = $this->pageService->updatePage($page, $pageDTO, 'update', 'Updated page');

        $this->assertEquals('updated-slug', $updatedPage->slug);
        $this->assertEquals(['en' => 'Updated Title', 'fr' => 'Titre Mis à Jour'], $updatedPage->getTranslations('title'));
        $this->assertEquals(['en' => 'Updated Meta Title'], $updatedPage->getTranslations('meta_title'));
        $this->assertEquals(['en' => 'Updated meta description'], $updatedPage->getTranslations('meta_description'));
        $this->assertTrue($updatedPage->no_index);
    }

    /** @test */
    public function it_can_delete_a_page(): void
    {
        $page = Page::factory()->create();

        $this->pageService->deletePage($page);

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    /** @test */
    public function it_can_get_pages_with_filters(): void
    {
        // Create test pages
        Page::factory()->create([
            'title' => ['en' => 'First Page'],
            'slug' => 'first-page',
        ]);
        Page::factory()->create([
            'title' => ['en' => 'Second Page'],
            'slug' => 'second-page',
        ]);

        $result = $this->pageService->getPagesWithFilters(
            'First',
            [],
            'title',
            'asc',
            10
        );

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertEquals('First Page', $result->first()->getTranslation('title', 'en'));
    }

    /** @test */
    public function it_can_get_page_with_content(): void
    {
        $page = Page::factory()->create();

        $result = $this->pageService->getPageWithContent($page);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($page->id, $result->id);
    }

    /** @test */
    public function it_can_validate_page_data(): void
    {
        $pageData = [
            'title' => ['en' => ''],
            'slug' => '',
        ];

        $pageDTO = PageDTO::fromArray($pageData);
        $errors = $this->pageService->validatePageData($pageDTO);

        $this->assertArrayHasKey('title.en', $errors);
        $this->assertArrayHasKey('slug', $errors);
    }

    /** @test */
    public function it_can_generate_unique_slug(): void
    {
        Page::factory()->create(['slug' => 'test-page']);

        $uniqueSlug = $this->pageService->generateUniqueSlug('test-page');

        $this->assertEquals('test-page-1', $uniqueSlug);
    }

    /** @test */
    public function it_can_get_pages_by_status(): void
    {
        $publishedPage = Page::factory()->create(['status' => \App\Enums\PublishStatus::PUBLISHED]);
        $draftPage = Page::factory()->create(['status' => \App\Enums\PublishStatus::DRAFT]);

        $publishedPages = $this->pageService->getPagesByStatus('published');
        $draftPages = $this->pageService->getPagesByStatus('draft');

        $this->assertEquals(1, $publishedPages->count());
        $this->assertEquals($publishedPage->id, $publishedPages->first()->id);
        $this->assertEquals(1, $draftPages->count());
        $this->assertEquals($draftPage->id, $draftPages->first()->id);
    }

    /** @test */
    public function it_can_search_pages(): void
    {
        Page::factory()->create([
            'title' => ['en' => 'Searchable Page'],
            'slug' => 'searchable-page',
        ]);
        Page::factory()->create([
            'title' => ['en' => 'Another Page'],
            'slug' => 'another-page',
        ]);

        $results = $this->pageService->searchPages('Searchable');

        $this->assertEquals(1, $results->count());
        $this->assertEquals('Searchable Page', $results->first()->getTranslation('title', 'en'));
    }

    /** @test */
    public function it_can_get_page_by_slug(): void
    {
        $page = Page::factory()->create(['slug' => 'test-slug']);

        $result = $this->pageService->getPageBySlug('test-slug');

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($page->id, $result->id);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_slug(): void
    {
        $result = $this->pageService->getPageBySlug('nonexistent-slug');

        $this->assertNull($result);
    }
} 