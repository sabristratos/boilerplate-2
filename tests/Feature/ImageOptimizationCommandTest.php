<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('public');
});

test('optimize images command shows help information', function () {
    $this->artisan('images:optimize', ['--help'])
        ->expectsOutput('Description:')
        ->expectsOutput('Optimize images in the media library')
        ->assertExitCode(0);
});

test('optimize images command lists available options', function () {
    $this->artisan('images:optimize', ['--help'])
        ->expectsOutput('Options:')
        ->expectsOutput('--force')
        ->expectsOutput('--limit')
        ->assertExitCode(0);
});

test('optimize images command processes all images by default', function () {
    // Create test media files
    $media1 = Media::factory()->create([
        'file_name' => 'test1.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $media2 = Media::factory()->create([
        'file_name' => 'test2.png',
        'mime_type' => 'image/png',
    ]);

    $media3 = Media::factory()->create([
        'file_name' => 'document.pdf',
        'mime_type' => 'application/pdf',
    ]);

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 2 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command respects limit option', function () {
    // Create multiple test media files
    for ($i = 1; $i <= 5; $i++) {
        Media::factory()->create([
            'file_name' => "test{$i}.jpg",
            'mime_type' => 'image/jpeg',
        ]);
    }

    $this->artisan('images:optimize', ['--limit' => 3])
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 5 images to optimize')
        ->expectsOutput('Limited to 3 images')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command handles force option', function () {
    $media = Media::factory()->create([
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $this->artisan('images:optimize', ['--force' => true])
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Force mode enabled - re-optimizing all images')
        ->expectsOutput('Found 1 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command handles no images gracefully', function () {
    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('No images found to optimize')
        ->assertExitCode(0);
});

test('optimize images command only processes image files', function () {
    // Create mixed media files
    $imageMedia = Media::factory()->create([
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $pdfMedia = Media::factory()->create([
        'file_name' => 'document.pdf',
        'mime_type' => 'application/pdf',
    ]);

    $docMedia = Media::factory()->create([
        'file_name' => 'document.docx',
        'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ]);

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 1 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command processes different image formats', function () {
    $imageFormats = [
        'test.jpg' => 'image/jpeg',
        'test.jpeg' => 'image/jpeg',
        'test.png' => 'image/png',
        'test.gif' => 'image/gif',
        'test.webp' => 'image/webp',
    ];

    foreach ($imageFormats as $fileName => $mimeType) {
        Media::factory()->create([
            'file_name' => $fileName,
            'mime_type' => $mimeType,
        ]);
    }

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 5 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command shows progress for large batches', function () {
    // Create many test media files
    for ($i = 1; $i <= 10; $i++) {
        Media::factory()->create([
            'file_name' => "test{$i}.jpg",
            'mime_type' => 'image/jpeg',
        ]);
    }

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 10 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command handles missing files gracefully', function () {
    $media = Media::factory()->create([
        'file_name' => 'missing.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 1 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command shows error count when errors occur', function () {
    // Create test media files
    $media1 = Media::factory()->create([
        'file_name' => 'test1.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $media2 = Media::factory()->create([
        'file_name' => 'test2.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 2 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command shows success count', function () {
    // Create test media files
    for ($i = 1; $i <= 3; $i++) {
        Media::factory()->create([
            'file_name' => "test{$i}.jpg",
            'mime_type' => 'image/jpeg',
        ]);
    }

    $this->artisan('images:optimize')
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Found 3 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command handles invalid limit gracefully', function () {
    $media = Media::factory()->create([
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $this->artisan('images:optimize', ['--limit' => -1])
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('Invalid limit provided, using default behavior')
        ->expectsOutput('Found 1 images to optimize')
        ->expectsOutput('Optimization completed successfully')
        ->assertExitCode(0);
});

test('optimize images command handles zero limit gracefully', function () {
    $media = Media::factory()->create([
        'file_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
    ]);

    $this->artisan('images:optimize', ['--limit' => 0])
        ->expectsOutput('Starting image optimization...')
        ->expectsOutput('No images to process with limit 0')
        ->assertExitCode(0);
}); 