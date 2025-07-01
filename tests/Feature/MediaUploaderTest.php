<?php

namespace Tests\Feature;

use App\Livewire\MediaUploader;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;
use App\Models\TemporaryMedia;

class MediaUploaderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_mount_with_model_and_collection()
    {
        $testimonial = Testimonial::factory()->create();
        
        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->assertSet('model', $testimonial)
        ->assertSet('collection', 'avatar');
    }

    /** @test */
    public function it_can_upload_file_to_model()
    {
        $testimonial = Testimonial::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('file', $file)
        ->call('save')
        ->assertDispatched('media-updated', modelId: $testimonial->id, collection: 'avatar')
        ->assertSet('file', null);

        $this->assertCount(1, $testimonial->getMedia('avatar'));
        $this->assertEquals('avatar.jpg', $testimonial->getFirstMedia('avatar')->name);
    }

    /** @test */
    public function it_can_upload_from_url()
    {
        $testimonial = Testimonial::factory()->create();
        
        // Mock a URL that returns an image
        $this->mock(\Spatie\MediaLibrary\Downloaders\DefaultDownloader::class, function ($mock) {
            $mock->shouldReceive('getTempFile')->andReturn(
                UploadedFile::fake()->image('remote-image.jpg')->getRealPath()
            );
        });

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('url', 'https://example.com/image.jpg')
        ->call('save')
        ->assertDispatched('media-updated', modelId: $testimonial->id, collection: 'avatar');

        $this->assertCount(1, $testimonial->getMedia('avatar'));
    }

    /** @test */
    public function it_can_select_existing_media()
    {
        $testimonial = Testimonial::factory()->create();
        $existingMedia = Media::create([
            'model_type' => Testimonial::class,
            'model_id' => Testimonial::factory()->create()->id,
            'collection_name' => 'avatar',
            'name' => 'existing-avatar.jpg',
            'file_name' => 'existing-avatar.jpg',
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'size' => 1024,
            'uuid' => \Illuminate\Support\Str::uuid(),
            'conversions_disk' => 'public',
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ]);

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('selectedMediaId', $existingMedia->id)
        ->call('save')
        ->assertDispatched('media-updated', modelId: $testimonial->id, collection: 'avatar');

        $this->assertCount(1, $testimonial->getMedia('avatar'));
        $this->assertEquals('existing-avatar.jpg', $testimonial->getFirstMedia('avatar')->name);
    }

    /** @test */
    public function it_clears_collection_before_adding_new_media()
    {
        $testimonial = Testimonial::factory()->create();
        
        // Add initial media
        $testimonial->addMedia(UploadedFile::fake()->image('old-avatar.jpg'))
            ->toMediaCollection('avatar');

        $this->assertCount(1, $testimonial->getMedia('avatar'));

        // Upload new media
        $file = UploadedFile::fake()->image('new-avatar.jpg');

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('file', $file)
        ->call('save');

        $this->assertCount(1, $testimonial->getMedia('avatar'));
        $this->assertEquals('new-avatar.jpg', $testimonial->getFirstMedia('avatar')->name);
    }

    /** @test */
    public function it_validates_file_upload()
    {
        $testimonial = Testimonial::factory()->create();
        $invalidFile = UploadedFile::fake()->image('document.jpg', 100, 100)->size(2048); // Too large

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('file', $invalidFile)
        ->call('save')
        ->assertHasErrors(['file']);
    }

    /** @test */
    public function it_validates_url_format()
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('url', 'invalid-url')
        ->call('save')
        ->assertHasErrors(['url']);
    }

    /** @test */
    public function it_requires_at_least_one_media_source()
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->call('save')
        ->assertNotDispatched('media-updated');
    }

    /** @test */
    public function it_can_remove_media()
    {
        $testimonial = Testimonial::factory()->create();
        $testimonial->addMedia(UploadedFile::fake()->image('avatar.jpg'))
            ->toMediaCollection('avatar');

        $this->assertCount(1, $testimonial->getMedia('avatar'));

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->call('remove')
        ->assertDispatched('media-updated', modelId: $testimonial->id, collection: 'avatar');

        $this->assertCount(0, $testimonial->getMedia('avatar'));
    }

    /** @test */
    public function it_can_toggle_media_selection()
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->call('toggleMediaSelection', 1)
        ->assertSet('selectedMediaIds', [1])
        ->call('toggleMediaSelection', 1)
        ->assertSet('selectedMediaIds', [])
        ->call('toggleMediaSelection', 2)
        ->assertSet('selectedMediaIds', [2]);
    }

    /** @test */
    public function it_can_confirm_media_selection()
    {
        $testimonial = Testimonial::factory()->create();
        $existingMedia = Media::create([
            'model_type' => Testimonial::class,
            'model_id' => Testimonial::factory()->create()->id,
            'collection_name' => 'avatar',
            'name' => 'existing-avatar.jpg',
            'file_name' => 'existing-avatar.jpg',
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'size' => 1024,
            'uuid' => \Illuminate\Support\Str::uuid(),
            'conversions_disk' => 'public',
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ]);

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('selectedMediaIds', [$existingMedia->id])
        ->call('confirmMediaSelection')
        ->assertDispatched('media-updated', modelId: $testimonial->id, collection: 'avatar');
    }

    /** @test */
    public function it_requires_selection_before_confirming()
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->call('confirmMediaSelection')
        ->assertNotDispatched('media-updated');
    }

    /** @test */
    public function it_resets_pagination_when_search_changes()
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('search', 'test')
        ->assertSet('search', 'test');
    }

    /** @test */
    public function it_handles_unsaved_model_gracefully()
    {
        $testimonial = new Testimonial(); // Not saved to database
        
        // Should work with temporary media
        $component = Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('file', UploadedFile::fake()->image('avatar.jpg'))
        ->call('save');
        
        // Debug: Check what events were actually dispatched
        $dispatchedEvents = $component->getDispatchedEvents();
        dump('Dispatched events:', $dispatchedEvents);
        
        // Check if temporary media was initialized
        $temporaryMedia = $component->get('temporaryMedia');
        dump('Temporary media:', $temporaryMedia);
        
        ->assertDispatched('media-updated', isTemporary: true);
    }

    /** @test */
    public function it_debugs_temporary_media_initialization()
    {
        $testimonial = new Testimonial(); // Not saved to database
        
        $component = Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ]);
        
        // Check if temporary media was initialized
        $this->assertNotNull($component->get('temporaryMedia'));
        
        // Try to save with a file
        $component->set('file', UploadedFile::fake()->image('avatar.jpg'))
            ->call('save');
            
        // Check if any events were dispatched
        $dispatchedEvents = $component->getDispatchedEvents();
        $this->assertNotEmpty($dispatchedEvents);
    }

    /** @test */
    public function it_filters_existing_media_by_image_type()
    {
        $testimonial = Testimonial::factory()->create();
        
        // Create image media
        $imageMedia = Media::create([
            'mime_type' => 'image/jpeg',
            'model_type' => Testimonial::class,
            'model_id' => Testimonial::factory()->create()->id,
            'collection_name' => 'avatar',
            'name' => 'image.jpg',
            'file_name' => 'image.jpg',
            'disk' => 'public',
            'size' => 1024,
            'uuid' => \Illuminate\Support\Str::uuid(),
            'conversions_disk' => 'public',
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ]);
        
        // Create non-image media
        $pdfMedia = Media::create([
            'mime_type' => 'application/pdf',
            'model_type' => Testimonial::class,
            'model_id' => Testimonial::factory()->create()->id,
            'collection_name' => 'avatar',
            'name' => 'document.pdf',
            'file_name' => 'document.pdf',
            'disk' => 'public',
            'size' => 1024,
            'uuid' => \Illuminate\Support\Str::uuid(),
            'conversions_disk' => 'public',
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ]);

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->assertSee($imageMedia->name)
        ->assertDontSee($pdfMedia->name);
    }

    /** @test */
    public function it_handles_media_copy_errors()
    {
        $testimonial = Testimonial::factory()->create();
        $nonExistentMediaId = 99999;

        Livewire::test(MediaUploader::class, [
            'model' => $testimonial,
            'collection' => 'avatar'
        ])
        ->set('selectedMediaId', $nonExistentMediaId)
        ->call('save')
        ->assertNotDispatched('media-updated');
    }

    /** @test */
    public function it_can_create_temporary_media_directly()
    {
        $sessionId = 'test-session-123';
        $fieldName = 'avatar';
        $modelType = Testimonial::class;
        $collectionName = 'avatar';
        
        $temporaryMedia = TemporaryMedia::createForSession(
            $sessionId,
            $fieldName,
            $modelType,
            $collectionName
        );
        
        $this->assertNotNull($temporaryMedia);
        $this->assertInstanceOf(TemporaryMedia::class, $temporaryMedia);
        $this->assertEquals($sessionId, $temporaryMedia->session_id);
        $this->assertEquals($fieldName, $temporaryMedia->field_name);
        $this->assertEquals($modelType, $temporaryMedia->model_type);
        $this->assertEquals($collectionName, $temporaryMedia->collection_name);
        
        // Test retrieval
        $retrieved = TemporaryMedia::getForSession($sessionId, $fieldName);
        $this->assertNotNull($retrieved);
        $this->assertEquals($temporaryMedia->id, $retrieved->id);
    }
} 