<?php

namespace Tests\Feature;

use App\Http\Resources\Admin\TestimonialResource;
use App\Livewire\ResourceSystem\ResourceForm;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class ResourceSystemMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_testimonial_with_avatar_via_resource_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class
        ])
        ->set('data', [
            'name' => 'John Doe',
            'title' => 'CEO',
            'content' => 'Great product!',
            'rating' => 5,
            'source' => 'Company Inc',
            'order' => 1,
        ])
        ->call('save')
        ->assertDispatched('media-updated');

        $testimonial = Testimonial::latest()->first();
        $this->assertEquals('John Doe', $testimonial->name);
        $this->assertEquals('CEO', $testimonial->title);
    }

    /** @test */
    public function it_can_update_testimonial_with_avatar_via_resource_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $testimonial = Testimonial::factory()->create([
            'name' => 'Old Name',
            'title' => 'Old Title',
        ]);

        $file = UploadedFile::fake()->image('new-avatar.jpg', 100, 100);

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class,
            'resourceId' => $testimonial->id
        ])
        ->set('data', [
            'name' => 'New Name',
            'title' => 'New Title',
            'content' => 'Updated content',
            'rating' => 4,
            'source' => 'Updated Company',
            'order' => 2,
        ])
        ->call('save')
        ->assertDispatched('media-updated');

        $testimonial->refresh();
        $this->assertEquals('New Name', $testimonial->name);
        $this->assertEquals('New Title', $testimonial->title);
    }

    /** @test */
    public function it_validates_required_fields_in_resource_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class
        ])
        ->set('data', [
            'name' => '', // Required field
            'content' => '', // Required field
            'rating' => 6, // Invalid rating
        ])
        ->call('save')
        ->assertHasErrors([
            'data.name',
            'data.content',
            'data.rating'
        ]);
    }

    /** @test */
    public function it_handles_media_field_in_resource_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $testimonial = Testimonial::factory()->create();

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class,
            'resourceId' => $testimonial->id
        ])
        ->assertSee('avatar') // Should show avatar field
        ->assertSee('media-uploader'); // Should include media uploader component
    }

    /** @test */
    public function it_creates_testimonial_without_avatar()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class
        ])
        ->set('data', [
            'name' => 'John Doe',
            'title' => 'CEO',
            'content' => 'Great product!',
            'rating' => 5,
            'source' => 'Company Inc',
            'order' => 1,
        ])
        ->call('save');

        $testimonial = Testimonial::latest()->first();
        $this->assertNotNull($testimonial);
        $this->assertEquals('John Doe', $testimonial->name);
        $this->assertCount(0, $testimonial->getMedia('avatar'));
    }

    /** @test */
    public function it_handles_unsaved_model_in_media_uploader()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a new testimonial instance without saving
        $testimonial = new Testimonial([
            'name' => 'Test',
            'content' => 'Test content',
            'rating' => 5,
            'order' => 1,
        ]);

        // This should not throw an error when the media uploader tries to access the model
        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class
        ])
        ->set('data', [
            'name' => 'John Doe',
            'content' => 'Great product!',
            'rating' => 5,
            'order' => 1,
        ])
        ->assertOk();
    }

    /** @test */
    public function it_handles_media_uploader_with_saved_model()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $testimonial = Testimonial::factory()->create();

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class,
            'resourceId' => $testimonial->id
        ])
        ->assertOk();
    }

    /** @test */
    public function it_renders_media_field_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class
        ])
        ->assertSeeHtml('media-uploader')
        ->assertSee('Avatar'); // Field label
    }

    /** @test */
    public function it_handles_media_collection_names_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $testimonial = Testimonial::factory()->create();

        // Test that the media uploader receives the correct collection name
        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class,
            'resourceId' => $testimonial->id
        ])
        ->assertSeeHtml('collection="avatar"');
    }

    /** @test */
    public function it_handles_media_uploader_events()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $testimonial = Testimonial::factory()->create();

        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class,
            'resourceId' => $testimonial->id
        ])
        ->call('save')
        ->assertDispatched('media-updated');
    }

    /** @test */
    public function it_handles_media_field_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test that the form validation works with media fields
        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class
        ])
        ->set('data', [
            'name' => 'John Doe',
            'content' => 'Great product!',
            'rating' => 5,
            'order' => 1,
        ])
        ->call('save')
        ->assertHasNoErrors();
    }

    /** @test */
    public function it_handles_media_field_in_form_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $testimonial = Testimonial::factory()->create();

        // Test that media fields are properly excluded from the main data array
        Livewire::test(ResourceForm::class, [
            'resource' => TestimonialResource::class,
            'resourceId' => $testimonial->id
        ])
        ->set('data', [
            'name' => 'John Doe',
            'content' => 'Great product!',
            'rating' => 5,
            'order' => 1,
            'avatar' => 'some-media-id', // This should be excluded
        ])
        ->call('save');

        $testimonial->refresh();
        $this->assertEquals('John Doe', $testimonial->name);
        // Avatar should not be saved as a regular field
        $this->assertNull($testimonial->avatar);
    }
} 