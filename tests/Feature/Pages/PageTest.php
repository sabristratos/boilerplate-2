<?php

namespace Tests\Feature\Pages;

use App\Models\ContentBlock;
use App\Models\Page;
use App\Enums\ContentBlockStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_page()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
        ]);

        $this->assertEquals('Test Page', $page->title);
        $this->assertEquals('test-page', $page->slug);
    }

    public function test_can_update_a_page()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $page->update([
            'title' => 'Updated Page',
            'slug' => 'updated-page',
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
        ]);

        $page->refresh();
        $this->assertEquals('Updated Page', $page->title);
        $this->assertEquals('updated-page', $page->slug);
    }

    public function test_can_delete_a_page()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $pageId = $page->id;
        $page->delete();

        $this->assertDatabaseMissing('pages', [
            'id' => $pageId,
        ]);
    }

    public function test_handles_translations()
    {
        $page = Page::create([
            'title' => 'English Title',
            'slug' => 'english-slug',
        ]);

        // Add translations
        $page->setTranslation('title', 'fr', 'Titre Français');
        $page->setTranslation('slug', 'fr', 'titre-francais');
        $page->save();

        // Check English (default)
        $this->assertEquals('English Title', $page->title);
        $this->assertEquals('english-slug', $page->slug);

        // Check French
        App::setLocale('fr');
        $this->assertEquals('Titre Français', $page->title);
        $this->assertEquals('titre-francais', $page->slug);

        // Reset locale
        App::setLocale('en');
    }

    public function test_can_check_if_translation_exists()
    {
        $page = Page::create([
            'title' => 'English Title',
            'slug' => 'english-slug',
        ]);

        // Add French translation
        $page->setTranslation('title', 'fr', 'Titre Français');
        $page->setTranslation('slug', 'fr', 'titre-francais');
        $page->save();

        $this->assertTrue($page->hasTranslation('en'));
        $this->assertTrue($page->hasTranslation('fr'));
        $this->assertFalse($page->hasTranslation('de'));
    }

    public function test_can_attach_media()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        // Create a test image
        $imagePath = storage_path('app/public/test-page-image.jpg');
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }
        file_put_contents($imagePath, 'test image content');

        // Add media to the page
        $media = $page->addMedia($imagePath)
            ->toMediaCollection('images');

        $this->assertCount(1, $page->getMedia('images'));
        $this->assertEquals('test-page-image.jpg', $page->getFirstMedia('images')->file_name);

        // Clean up
        @unlink($imagePath);
    }

    public function test_has_many_content_blocks()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        // Create content blocks for the page
        ContentBlock::create([
            'type' => 'hero-section',
            'page_id' => $page->id,
            'data' => ['heading' => 'First Block'],
            'status' => ContentBlockStatus::PUBLISHED,
        ]);

        ContentBlock::create([
            'type' => 'content-area',
            'page_id' => $page->id,
            'data' => ['content' => 'Second Block'],
            'status' => ContentBlockStatus::PUBLISHED,
        ]);

        $this->assertCount(2, $page->contentBlocks);
        $this->assertInstanceOf(ContentBlock::class, $page->contentBlocks->first());
    }

    public function test_resolves_route_binding_by_id()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $resolvedPage = (new Page())->resolveRouteBinding($page->id);

        $this->assertInstanceOf(Page::class, $resolvedPage);
        $this->assertEquals($page->id, $resolvedPage->id);
    }

    public function test_resolves_route_binding_by_slug()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $resolvedPage = (new Page())->resolveRouteBinding('test-page');

        $this->assertInstanceOf(Page::class, $resolvedPage);
        $this->assertEquals($page->id, $resolvedPage->id);
    }

    public function test_resolves_route_binding_by_translated_slug()
    {
        $page = Page::create([
            'title' => 'English Title',
            'slug' => 'english-slug',
        ]);

        // Add French translation
        $page->setTranslation('title', 'fr', 'Titre Français');
        $page->setTranslation('slug', 'fr', 'titre-francais');
        $page->save();

        // Set locale to French
        App::setLocale('fr');

        $resolvedPage = (new Page())->resolveRouteBinding('titre-francais');

        $this->assertInstanceOf(Page::class, $resolvedPage);
        $this->assertEquals($page->id, $resolvedPage->id);

        // Reset locale
        App::setLocale('en');
    }

    public function test_uses_slug_as_route_key_name()
    {
        $page = new Page();
        $this->assertEquals('slug', $page->getRouteKeyName());
    }
}
