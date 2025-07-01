<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('page displays basic meta tags', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_title' => ['en' => 'SEO Title'],
        'meta_description' => ['en' => 'SEO Description'],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<title>SEO Title</title>');
    $response->assertSee('<meta name="description" content="SEO Description">');
});

test('page displays open graph meta tags', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'og_title' => ['en' => 'OG Title'],
        'og_description' => ['en' => 'OG Description'],
        'og_image' => ['en' => 'https://example.com/og-image.jpg'],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta property="og:title" content="OG Title">');
    $response->assertSee('<meta property="og:description" content="OG Description">');
    $response->assertSee('<meta property="og:image" content="https://example.com/og-image.jpg">');
    $response->assertSee('<meta property="og:type" content="website">');
    $response->assertSee('<meta property="og:url" content="' . url('/test-page') . '">');
});

test('page displays twitter card meta tags', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'twitter_title' => ['en' => 'Twitter Title'],
        'twitter_description' => ['en' => 'Twitter Description'],
        'twitter_image' => ['en' => 'https://example.com/twitter-image.jpg'],
        'twitter_card_type' => ['en' => 'summary_large_image'],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="twitter:title" content="Twitter Title">');
    $response->assertSee('<meta name="twitter:description" content="Twitter Description">');
    $response->assertSee('<meta name="twitter:image" content="https://example.com/twitter-image.jpg">');
    $response->assertSee('<meta name="twitter:card" content="summary_large_image">');
});

test('page displays canonical url', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'canonical_url' => ['en' => 'https://example.com/canonical'],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<link rel="canonical" href="https://example.com/canonical">');
});

test('page displays structured data', function () {
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => 'Test Article',
        'author' => [
            '@type' => 'Person',
            'name' => 'John Doe'
        ]
    ];

    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'structured_data' => ['en' => json_encode($structuredData)],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<script type="application/ld+json">');
    $response->assertSee('"@context":"https://schema.org"');
    $response->assertSee('"@type":"Article"');
    $response->assertSee('"headline":"Test Article"');
});

test('page displays robots meta tags', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'no_index' => true,
        'no_follow' => true,
        'no_archive' => false,
        'no_snippet' => true,
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">');
});

test('page displays meta keywords', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_keywords' => ['en' => 'keyword1, keyword2, keyword3'],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="keywords" content="keyword1, keyword2, keyword3">');
});

test('page falls back to title when meta title is not set', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page Title'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_title' => null,
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<title>Test Page Title</title>');
});

test('page falls back to description when meta description is not set', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_description' => null,
        'description' => ['en' => 'Page Description'],
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="description" content="Page Description">');
});

test('page displays default robots meta when no robots settings', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'no_index' => false,
        'no_follow' => false,
        'no_archive' => false,
        'no_snippet' => false,
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="robots" content="index,follow,archive,snippet">');
});

test('page displays google analytics when enabled', function () {
    // Set Google Analytics ID in settings
    config(['settings.seo.google_analytics_id' => 'GA-123456789']);

    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('gtag(\'config\', \'GA-123456789\');');
});

test('page displays google tag manager when enabled', function () {
    // Set Google Tag Manager ID in settings
    config(['settings.seo.google_tag_manager_id' => 'GTM-ABC123']);

    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('gtm.js');
    $response->assertSee('GTM-ABC123');
});

test('page does not display analytics when not configured', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertDontSee('gtag');
    $response->assertDontSee('gtm.js');
});

test('page handles missing translations gracefully', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_title' => ['fr' => 'French Title'], // No English translation
        'meta_description' => ['fr' => 'French Description'], // No English translation
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    // Should fall back to title
    $response->assertSee('<title>Test Page</title>');
    // Should not display meta description if no English translation
    $response->assertDontSee('<meta name="description"');
});

test('page displays charset and viewport meta tags', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta charset="utf-8">');
    $response->assertSee('<meta name="viewport" content="width=device-width, initial-scale=1">');
});

test('page displays language meta tag', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<html lang="en">');
});

test('page displays favicon links', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<link rel="icon" type="image/svg+xml" href="/favicon.svg">');
    $response->assertSee('<link rel="icon" href="/favicon.ico">');
    $response->assertSee('<link rel="apple-touch-icon" href="/apple-touch-icon.png">');
});

test('page displays theme color meta tag', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="theme-color" content="#ffffff">');
});

test('page displays color scheme meta tag', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $response->assertSee('<meta name="color-scheme" content="light dark">');
}); 