<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\Sitemap;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('sitemap command generates xml sitemap', function () {
    // Create some test pages
    $publishedPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'test-page',
    ]);

    $draftPage = Page::factory()->create([
        'status' => 'draft',
        'slug' => 'draft-page',
    ]);

    $noIndexPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => true,
        'slug' => 'no-index-page',
    ]);

    // Run the sitemap generation command
    $this->artisan('sitemap:generate')
        ->expectsOutput('Generating sitemap...')
        ->expectsOutput('Sitemap generated successfully at: ' . public_path('sitemap.xml'))
        ->expectsOutput('Total URLs: 2') // Homepage + 1 published page
        ->assertExitCode(0);

    // Check if sitemap file was created
    expect(File::exists(public_path('sitemap.xml')))->toBeTrue();

    // Read and parse the sitemap
    $sitemapContent = File::get(public_path('sitemap.xml'));
    
    // Should contain homepage
    expect($sitemapContent)->toContain('<loc>' . url('/') . '</loc>');
    
    // Should contain published page
    expect($sitemapContent)->toContain('<loc>' . url('/test-page') . '</loc>');
    
    // Should not contain draft page
    expect($sitemapContent)->not->toContain('draft-page');
    
    // Should not contain no-index page
    expect($sitemapContent)->not->toContain('no-index-page');
});

test('sitemap command generates txt sitemap', function () {
    $publishedPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'test-page',
    ]);

    $this->artisan('sitemap:generate', ['--format' => 'txt'])
        ->expectsOutput('Generating sitemap...')
        ->expectsOutput('Sitemap generated successfully at: ' . public_path('sitemap.txt'))
        ->expectsOutput('Total URLs: 2')
        ->assertExitCode(0);

    expect(File::exists(public_path('sitemap.txt')))->toBeTrue();

    $sitemapContent = File::get(public_path('sitemap.txt'));
    $urls = explode("\n", trim($sitemapContent));
    
    expect($urls)->toHaveCount(2);
    expect($urls)->toContain(url('/'));
    expect($urls)->toContain(url('/test-page'));
});

test('sitemap route serves xml sitemap', function () {
    $publishedPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'test-page',
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee('<loc>' . url('/') . '</loc>');
    $response->assertSee('<loc>' . url('/test-page') . '</loc>');
});

test('sitemap route serves txt sitemap', function () {
    $publishedPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'test-page',
    ]);

    $response = $this->get('/sitemap.txt');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/plain');
    $response->assertSee(url('/'));
    $response->assertSee(url('/test-page'));
});

test('sitemap includes important routes when pages exist', function () {
    // Create pages with specific slugs
    $contactPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'contact',
    ]);

    $aboutPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'about',
    ]);

    $servicesPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'services',
    ]);

    $this->artisan('sitemap:generate')->assertExitCode(0);

    $sitemapContent = File::get(public_path('sitemap.xml'));
    
    expect($sitemapContent)->toContain('<loc>' . url('/contact') . '</loc>');
    expect($sitemapContent)->toContain('<loc>' . url('/about') . '</loc>');
    expect($sitemapContent)->toContain('<loc>' . url('/services') . '</loc>');
});

test('sitemap excludes important routes when pages do not exist', function () {
    $this->artisan('sitemap:generate')->assertExitCode(0);

    $sitemapContent = File::get(public_path('sitemap.xml'));
    
    expect($sitemapContent)->not->toContain('contact');
    expect($sitemapContent)->not->toContain('about');
    expect($sitemapContent)->not->toContain('services');
});

test('sitemap includes correct change frequency and priority', function () {
    $publishedPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'test-page',
    ]);

    $this->artisan('sitemap:generate')->assertExitCode(0);

    $sitemapContent = File::get(public_path('sitemap.xml'));
    
    // Homepage should have daily frequency and 1.0 priority
    expect($sitemapContent)->toContain('<changefreq>daily</changefreq>');
    expect($sitemapContent)->toContain('<priority>1.0</priority>');
    
    // Regular pages should have weekly frequency and 0.8 priority
    expect($sitemapContent)->toContain('<changefreq>weekly</changefreq>');
    expect($sitemapContent)->toContain('<priority>0.8</priority>');
});

test('sitemap includes last modification dates', function () {
    $publishedPage = Page::factory()->create([
        'status' => 'published',
        'no_index' => false,
        'slug' => 'test-page',
        'updated_at' => now()->subDays(5),
    ]);

    $this->artisan('sitemap:generate')->assertExitCode(0);

    $sitemapContent = File::get(public_path('sitemap.xml'));
    
    // Should include lastmod for the page
    expect($sitemapContent)->toContain('<lastmod>');
    expect($sitemapContent)->toContain($publishedPage->updated_at->toISOString());
});

test('sitemap handles empty database gracefully', function () {
    $this->artisan('sitemap:generate')
        ->expectsOutput('Generating sitemap...')
        ->expectsOutput('Sitemap generated successfully at: ' . public_path('sitemap.xml'))
        ->expectsOutput('Total URLs: 1') // Only homepage
        ->assertExitCode(0);

    $sitemapContent = File::get(public_path('sitemap.xml'));
    
    // Should only contain homepage
    expect($sitemapContent)->toContain('<loc>' . url('/') . '</loc>');
    expect($sitemapContent)->not->toContain('<loc>' . url('/') . '</loc>', 2); // Should not appear twice
}); 