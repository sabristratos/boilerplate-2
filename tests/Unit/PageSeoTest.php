<?php

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('page can be created with all seo fields', function () {
    $page = Page::create([
        'title' => ['en' => 'Test Page', 'fr' => 'Page de Test'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_title' => ['en' => 'SEO Title', 'fr' => 'Titre SEO'],
        'meta_description' => ['en' => 'SEO Description', 'fr' => 'Description SEO'],
        'meta_keywords' => ['en' => 'keyword1, keyword2', 'fr' => 'mot-clé1, mot-clé2'],
        'og_title' => ['en' => 'OG Title', 'fr' => 'Titre OG'],
        'og_description' => ['en' => 'OG Description', 'fr' => 'Description OG'],
        'og_image' => ['en' => 'https://example.com/og-image.jpg', 'fr' => 'https://example.com/og-image-fr.jpg'],
        'twitter_title' => ['en' => 'Twitter Title', 'fr' => 'Titre Twitter'],
        'twitter_description' => ['en' => 'Twitter Description', 'fr' => 'Description Twitter'],
        'twitter_image' => ['en' => 'https://example.com/twitter-image.jpg', 'fr' => 'https://example.com/twitter-image-fr.jpg'],
        'twitter_card_type' => ['en' => 'summary_large_image', 'fr' => 'summary_large_image'],
        'canonical_url' => ['en' => 'https://example.com/canonical', 'fr' => 'https://example.com/canonical-fr'],
        'structured_data' => ['en' => '{"@type": "Article"}', 'fr' => '{"@type": "Article"}'],
        'no_index' => false,
        'no_follow' => true,
        'no_archive' => false,
        'no_snippet' => true,
    ]);

    expect($page)->toBeInstanceOf(Page::class);
    expect($page->title['en'])->toBe('Test Page');
    expect($page->title['fr'])->toBe('Page de Test');
    expect($page->meta_title['en'])->toBe('SEO Title');
    expect($page->meta_description['en'])->toBe('SEO Description');
    expect($page->meta_keywords['en'])->toBe('keyword1, keyword2');
    expect($page->og_title['en'])->toBe('OG Title');
    expect($page->og_description['en'])->toBe('OG Description');
    expect($page->og_image['en'])->toBe('https://example.com/og-image.jpg');
    expect($page->twitter_title['en'])->toBe('Twitter Title');
    expect($page->twitter_description['en'])->toBe('Twitter Description');
    expect($page->twitter_image['en'])->toBe('https://example.com/twitter-image.jpg');
    expect($page->twitter_card_type['en'])->toBe('summary_large_image');
    expect($page->canonical_url['en'])->toBe('https://example.com/canonical');
    expect($page->structured_data['en'])->toBe('{"@type": "Article"}');
    expect($page->no_index)->toBeFalse();
    expect($page->no_follow)->toBeTrue();
    expect($page->no_archive)->toBeFalse();
    expect($page->no_snippet)->toBeTrue();
});

test('page can be updated with seo fields', function () {
    $page = Page::factory()->create([
        'meta_title' => ['en' => 'Old Title'],
        'meta_description' => ['en' => 'Old Description'],
        'no_index' => false,
    ]);

    $page->update([
        'meta_title' => ['en' => 'New Title', 'fr' => 'Nouveau Titre'],
        'meta_description' => ['en' => 'New Description', 'fr' => 'Nouvelle Description'],
        'no_index' => true,
        'no_follow' => true,
    ]);

    $page->refresh();

    expect($page->meta_title['en'])->toBe('New Title');
    expect($page->meta_title['fr'])->toBe('Nouveau Titre');
    expect($page->meta_description['en'])->toBe('New Description');
    expect($page->meta_description['fr'])->toBe('Nouvelle Description');
    expect($page->no_index)->toBeTrue();
    expect($page->no_follow)->toBeTrue();
});

test('page has translation support for all seo fields', function () {
    $page = Page::create([
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'slug' => 'test-page',
        'status' => 'published',
        'meta_title' => ['en' => 'English Meta Title', 'fr' => 'French Meta Title'],
        'meta_description' => ['en' => 'English Meta Description', 'fr' => 'French Meta Description'],
        'meta_keywords' => ['en' => 'english, keywords', 'fr' => 'français, mots-clés'],
        'og_title' => ['en' => 'English OG Title', 'fr' => 'French OG Title'],
        'og_description' => ['en' => 'English OG Description', 'fr' => 'French OG Description'],
        'og_image' => ['en' => 'https://example.com/en-og.jpg', 'fr' => 'https://example.com/fr-og.jpg'],
        'twitter_title' => ['en' => 'English Twitter Title', 'fr' => 'French Twitter Title'],
        'twitter_description' => ['en' => 'English Twitter Description', 'fr' => 'French Twitter Description'],
        'twitter_image' => ['en' => 'https://example.com/en-twitter.jpg', 'fr' => 'https://example.com/fr-twitter.jpg'],
        'twitter_card_type' => ['en' => 'summary', 'fr' => 'summary_large_image'],
        'canonical_url' => ['en' => 'https://example.com/en', 'fr' => 'https://example.com/fr'],
        'structured_data' => ['en' => '{"@type": "Article"}', 'fr' => '{"@type": "WebPage"}'],
    ]);

    // Test English translations
    expect($page->getTranslation('title', 'en'))->toBe('English Title');
    expect($page->getTranslation('meta_title', 'en'))->toBe('English Meta Title');
    expect($page->getTranslation('meta_description', 'en'))->toBe('English Meta Description');
    expect($page->getTranslation('meta_keywords', 'en'))->toBe('english, keywords');
    expect($page->getTranslation('og_title', 'en'))->toBe('English OG Title');
    expect($page->getTranslation('og_description', 'en'))->toBe('English OG Description');
    expect($page->getTranslation('og_image', 'en'))->toBe('https://example.com/en-og.jpg');
    expect($page->getTranslation('twitter_title', 'en'))->toBe('English Twitter Title');
    expect($page->getTranslation('twitter_description', 'en'))->toBe('English Twitter Description');
    expect($page->getTranslation('twitter_image', 'en'))->toBe('https://example.com/en-twitter.jpg');
    expect($page->getTranslation('twitter_card_type', 'en'))->toBe('summary');
    expect($page->getTranslation('canonical_url', 'en'))->toBe('https://example.com/en');
    expect($page->getTranslation('structured_data', 'en'))->toBe('{"@type": "Article"}');

    // Test French translations
    expect($page->getTranslation('title', 'fr'))->toBe('French Title');
    expect($page->getTranslation('meta_title', 'fr'))->toBe('French Meta Title');
    expect($page->getTranslation('meta_description', 'fr'))->toBe('French Meta Description');
    expect($page->getTranslation('meta_keywords', 'fr'))->toBe('français, mots-clés');
    expect($page->getTranslation('og_title', 'fr'))->toBe('French OG Title');
    expect($page->getTranslation('og_description', 'fr'))->toBe('French OG Description');
    expect($page->getTranslation('og_image', 'fr'))->toBe('https://example.com/fr-og.jpg');
    expect($page->getTranslation('twitter_title', 'fr'))->toBe('French Twitter Title');
    expect($page->getTranslation('twitter_description', 'fr'))->toBe('French Twitter Description');
    expect($page->getTranslation('twitter_image', 'fr'))->toBe('https://example.com/fr-twitter.jpg');
    expect($page->getTranslation('twitter_card_type', 'fr'))->toBe('summary_large_image');
    expect($page->getTranslation('canonical_url', 'fr'))->toBe('https://example.com/fr');
    expect($page->getTranslation('structured_data', 'fr'))->toBe('{"@type": "WebPage"}');
});

test('page can set individual translations', function () {
    $page = Page::create([
        'title' => ['en' => 'English Title'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    $page->setTranslation('meta_title', 'en', 'English Meta Title');
    $page->setTranslation('meta_title', 'fr', 'French Meta Title');
    $page->setTranslation('meta_description', 'en', 'English Meta Description');
    $page->setTranslation('og_title', 'fr', 'French OG Title');
    $page->save();

    expect($page->getTranslation('meta_title', 'en'))->toBe('English Meta Title');
    expect($page->getTranslation('meta_title', 'fr'))->toBe('French Meta Title');
    expect($page->getTranslation('meta_description', 'en'))->toBe('English Meta Description');
    expect($page->getTranslation('og_title', 'fr'))->toBe('French OG Title');
});

test('page has correct boolean casting for robots meta fields', function () {
    $page = Page::create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
        'no_index' => true,
        'no_follow' => false,
        'no_archive' => true,
        'no_snippet' => false,
    ]);

    expect($page->no_index)->toBeTrue();
    expect($page->no_follow)->toBeFalse();
    expect($page->no_archive)->toBeTrue();
    expect($page->no_snippet)->toBeFalse();

    // Test updating boolean fields
    $page->update([
        'no_index' => false,
        'no_follow' => true,
        'no_archive' => false,
        'no_snippet' => true,
    ]);

    $page->refresh();

    expect($page->no_index)->toBeFalse();
    expect($page->no_follow)->toBeTrue();
    expect($page->no_archive)->toBeFalse();
    expect($page->no_snippet)->toBeTrue();
});

test('page has translation checking functionality', function () {
    $page = Page::create([
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    expect($page->hasTranslation('title', 'en'))->toBeTrue();
    expect($page->hasTranslation('title', 'fr'))->toBeTrue();
    expect($page->hasTranslation('title', 'es'))->toBeFalse();
    expect($page->hasTranslation('meta_title', 'en'))->toBeFalse();
});

test('page can handle empty seo fields gracefully', function () {
    $page = Page::create([
        'title' => ['en' => 'Test Page'],
        'slug' => 'test-page',
        'status' => 'published',
    ]);

    // Test getting non-existent translations
    expect($page->getTranslation('meta_title', 'en'))->toBeNull();
    expect($page->getTranslation('meta_description', 'fr'))->toBeNull();
    expect($page->getTranslation('og_title', 'en'))->toBeNull();

    // Test setting translations
    $page->setTranslation('meta_title', 'en', '');
    $page->setTranslation('meta_description', 'en', null);
    $page->save();

    expect($page->getTranslation('meta_title', 'en'))->toBe('');
    expect($page->getTranslation('meta_description', 'en'))->toBeNull();
});

test('page factory creates pages with default seo values', function () {
    $page = Page::factory()->create();

    expect($page->title)->toBeArray();
    expect($page->meta_title)->toBeNull();
    expect($page->meta_description)->toBeNull();
    expect($page->no_index)->toBeFalse();
    expect($page->no_follow)->toBeFalse();
    expect($page->no_archive)->toBeFalse();
    expect($page->no_snippet)->toBeFalse();
});

test('page factory can create pages with seo fields', function () {
    $page = Page::factory()->create([
        'meta_title' => ['en' => 'Factory Meta Title'],
        'meta_description' => ['en' => 'Factory Meta Description'],
        'no_index' => true,
        'no_follow' => true,
    ]);

    expect($page->getTranslation('meta_title', 'en'))->toBe('Factory Meta Title');
    expect($page->getTranslation('meta_description', 'en'))->toBe('Factory Meta Description');
    expect($page->no_index)->toBeTrue();
    expect($page->no_follow)->toBeTrue();
}); 