<?php

namespace Tests\Feature\Pages;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PageModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_page()
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

    /** @test */
    public function it_can_update_a_page()
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
            'title' => json_encode(['en' => 'Updated Page']),
            'slug' => json_encode(['en' => 'updated-page']),
        ]);

        $this->assertEquals('Updated Page', $page->title);
        $this->assertEquals('updated-page', $page->slug);
    }

    /** @test */
    public function it_can_delete_a_page()
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

    /** @test */
    public function it_handles_translations()
    {
        $page = Page::create([
            'title' => 'English Title',
            'slug' => 'english-slug',
        ]);

        // Add translations
        $page->setTranslation('title', 'fr', 'Titre Français');
        $page->setTranslation('slug', 'fr', 'slug-francais');
        $page->save();

        // Check English (default)
        $this->assertEquals('English Title', $page->title);
        $this->assertEquals('english-slug', $page->slug);

        // Check French
        App::setLocale('fr');
        $this->assertEquals('Titre Français', $page->title);
        $this->assertEquals('slug-francais', $page->slug);

        // Check translations array
        $this->assertEquals([
            'en' => 'English Title',
            'fr' => 'Titre Français',
        ], $page->getTranslations('title'));

        // Check hasTranslation method
        $this->assertTrue($page->hasTranslation('en'));
        $this->assertTrue($page->hasTranslation('fr'));
        $this->assertFalse($page->hasTranslation('de'));
    }

    /** @test */
    public function it_resolves_route_binding_by_id()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $foundPage = $page->resolveRouteBinding($page->id);

        $this->assertInstanceOf(Page::class, $foundPage);
        $this->assertEquals($page->id, $foundPage->id);
    }

    /** @test */
    public function it_resolves_route_binding_by_slug()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        $foundPage = $page->resolveRouteBinding('test-page');

        $this->assertInstanceOf(Page::class, $foundPage);
        $this->assertEquals($page->id, $foundPage->id);
    }

    /** @test */
    public function it_resolves_route_binding_by_localized_slug()
    {
        $page = Page::create([
            'title' => 'English Title',
            'slug' => 'english-slug',
        ]);

        $page->setTranslation('title', 'fr', 'Titre Français');
        $page->setTranslation('slug', 'fr', 'slug-francais');
        $page->save();

        // Test with French locale
        App::setLocale('fr');
        $foundPage = $page->resolveRouteBinding('slug-francais');

        $this->assertInstanceOf(Page::class, $foundPage);
        $this->assertEquals($page->id, $foundPage->id);
    }

    /** @test */
    public function it_can_attach_media()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
        ]);

        // Create a test image
        $imagePath = storage_path('app/public/test-image.jpg');
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }
        file_put_contents($imagePath, 'test image content');

        // Add media to the page
        $media = $page->addMedia($imagePath)
            ->toMediaCollection('featured_image');

        $this->assertCount(1, $page->getMedia('featured_image'));
        $this->assertEquals('test-image.jpg', $page->getFirstMedia('featured_image')->file_name);

        // Clean up
        @unlink($imagePath);
    }
}
