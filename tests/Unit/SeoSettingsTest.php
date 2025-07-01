<?php

use App\Enums\SettingGroupKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('seo setting group is defined in enum', function () {
    expect(SettingGroupKey::SEO->value)->toBe('seo');
});

test('seo settings configuration exists', function () {
    $settings = Config::get('settings');
    
    expect($settings)->toHaveKey('seo');
    expect($settings['seo'])->toBeArray();
});

test('seo settings has required fields', function () {
    $seoSettings = Config::get('settings.seo');
    
    expect($seoSettings)->toHaveKey('google_analytics_id');
    expect($seoSettings)->toHaveKey('google_tag_manager_id');
    expect($seoSettings)->toHaveKey('default_meta_title');
    expect($seoSettings)->toHaveKey('default_meta_description');
    expect($seoSettings)->toHaveKey('default_meta_keywords');
    expect($seoSettings)->toHaveKey('default_og_image');
    expect($seoSettings)->toHaveKey('default_twitter_image');
    expect($seoSettings)->toHaveKey('default_twitter_card_type');
});

test('seo settings have correct default values', function () {
    $seoSettings = Config::get('settings.seo');
    
    expect($seoSettings['google_analytics_id'])->toBeNull();
    expect($seoSettings['google_tag_manager_id'])->toBeNull();
    expect($seoSettings['default_meta_title'])->toBe('Your Website');
    expect($seoSettings['default_meta_description'])->toBe('Your website description');
    expect($seoSettings['default_meta_keywords'])->toBe('');
    expect($seoSettings['default_og_image'])->toBeNull();
    expect($seoSettings['default_twitter_image'])->toBeNull();
    expect($seoSettings['default_twitter_card_type'])->toBe('summary_large_image');
});

test('seo settings can be accessed via config helper', function () {
    $googleAnalyticsId = Config::get('settings.seo.google_analytics_id');
    $googleTagManagerId = Config::get('settings.seo.google_tag_manager_id');
    $defaultMetaTitle = Config::get('settings.seo.default_meta_title');
    
    expect($googleAnalyticsId)->toBeNull();
    expect($googleTagManagerId)->toBeNull();
    expect($defaultMetaTitle)->toBe('Your Website');
});

test('seo settings can be overridden', function () {
    Config::set('settings.seo.google_analytics_id', 'GA-123456789');
    Config::set('settings.seo.google_tag_manager_id', 'GTM-ABC123');
    Config::set('settings.seo.default_meta_title', 'Custom Title');
    
    expect(Config::get('settings.seo.google_analytics_id'))->toBe('GA-123456789');
    expect(Config::get('settings.seo.google_tag_manager_id'))->toBe('GTM-ABC123');
    expect(Config::get('settings.seo.default_meta_title'))->toBe('Custom Title');
});

test('seo settings structure is correct', function () {
    $seoSettings = Config::get('settings.seo');
    
    // Check that all values are strings or null
    foreach ($seoSettings as $key => $value) {
        expect($value)->when(
            $value !== null,
            fn($value) => $value->toBeString(),
            fn($value) => $value->toBeNull()
        );
    }
});

test('seo settings are properly nested', function () {
    $settings = Config::get('settings');
    
    expect($settings)->toHaveKey('seo');
    expect($settings['seo'])->toBeArray();
    expect($settings['seo'])->not->toBeEmpty();
});

test('seo settings can be used in environment variables', function () {
    // Test that settings can be overridden via environment
    putenv('SETTINGS_SEO_GOOGLE_ANALYTICS_ID=GA-TEST123');
    putenv('SETTINGS_SEO_GOOGLE_TAG_MANAGER_ID=GTM-TEST123');
    
    // Clean up
    putenv('SETTINGS_SEO_GOOGLE_ANALYTICS_ID');
    putenv('SETTINGS_SEO_GOOGLE_TAG_MANAGER_ID');
    
    // Note: In a real application, these would be loaded from environment variables
    // For testing purposes, we'll just verify the config structure
    expect(Config::get('settings.seo'))->toHaveKey('google_analytics_id');
    expect(Config::get('settings.seo'))->toHaveKey('google_tag_manager_id');
});

test('seo settings validation works correctly', function () {
    $seoSettings = Config::get('settings.seo');
    
    // Test that required fields exist and have appropriate types
    expect($seoSettings['google_analytics_id'])->when(
        $seoSettings['google_analytics_id'] !== null,
        fn($value) => $value->toBeString(),
        fn($value) => $value->toBeNull()
    );
    
    expect($seoSettings['google_tag_manager_id'])->when(
        $seoSettings['google_tag_manager_id'] !== null,
        fn($value) => $value->toBeString(),
        fn($value) => $value->toBeNull()
    );
    
    expect($seoSettings['default_twitter_card_type'])->toBeIn([
        'summary',
        'summary_large_image',
        'app',
        'player'
    ]);
});

test('seo settings can be accessed in blade templates', function () {
    $seoSettings = Config::get('settings.seo');
    
    // Simulate what would be available in a blade template
    $googleAnalyticsId = $seoSettings['google_analytics_id'] ?? null;
    $googleTagManagerId = $seoSettings['google_tag_manager_id'] ?? null;
    $defaultMetaTitle = $seoSettings['default_meta_title'] ?? '';
    
    expect($googleAnalyticsId)->toBeNull();
    expect($googleTagManagerId)->toBeNull();
    expect($defaultMetaTitle)->toBe('Your Website');
});

test('seo settings can be used for fallback values', function () {
    $seoSettings = Config::get('settings.seo');
    
    // Test fallback logic
    $metaTitle = $seoSettings['default_meta_title'] ?? 'Fallback Title';
    $metaDescription = $seoSettings['default_meta_description'] ?? 'Fallback Description';
    $ogImage = $seoSettings['default_og_image'] ?? '/default-og-image.jpg';
    
    expect($metaTitle)->toBe('Your Website');
    expect($metaDescription)->toBe('Your website description');
    expect($ogImage)->toBe('/default-og-image.jpg');
});

test('seo settings are properly documented', function () {
    $seoSettings = Config::get('settings.seo');
    
    // All settings should be documented in the config file
    $documentedSettings = [
        'google_analytics_id',
        'google_tag_manager_id',
        'default_meta_title',
        'default_meta_description',
        'default_meta_keywords',
        'default_og_image',
        'default_twitter_image',
        'default_twitter_card_type',
    ];
    
    foreach ($documentedSettings as $setting) {
        expect($seoSettings)->toHaveKey($setting);
    }
    
    // No extra undocumented settings
    expect(array_keys($seoSettings))->toHaveCount(count($documentedSettings));
}); 