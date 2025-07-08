<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\FormElementType;
use App\Models\ContentBlock;
use App\Models\Form;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\RevisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevisionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_form_creates_revision_on_create(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Test Form'],
            'elements' => [['type' => FormElementType::TEXT->value]],
        ]);

        $this->assertTrue($form->hasRevisions());
        $this->assertEquals(1, $form->revision_count);

        $revision = $form->latestRevision();
        $this->assertNotNull($revision);
        $this->assertEquals('create', $revision->action);
        $this->assertEquals('1.0.0', $revision->version);
        $this->assertTrue($revision->is_published);
    }

    public function test_form_creates_revision_on_update(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Original Name'],
        ]);

        $form->update(['name' => ['en' => 'Updated Name']]);

        $this->assertEquals(2, $form->revision_count);

        $latestRevision = $form->latestRevision();
        $this->assertEquals('update', $latestRevision->action);
        $this->assertEquals('1.0.1', $latestRevision->version);
        $this->assertArrayHasKey('name', $latestRevision->changes);
    }

    public function test_form_creates_revision_on_publish(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Original Name'],
            'draft_name' => ['en' => 'Draft Name'],
        ]);

        $form->publishDraft();

        $this->assertEquals(2, $form->revision_count);

        $latestRevision = $form->latestRevision();
        $this->assertEquals('publish', $latestRevision->action);
        $this->assertEquals('2.0.0', $latestRevision->version);
        $this->assertTrue($latestRevision->is_published);
    }

    public function test_page_creates_revision_on_create(): void
    {
        $this->actingAs($this->user);

        $page = Page::factory()->create([
            'title' => ['en' => 'Test Page'],
            'slug' => 'test-page',
        ]);

        $this->assertTrue($page->hasRevisions());
        $this->assertEquals(1, $page->revision_count);

        $revision = $page->latestRevision();
        $this->assertNotNull($revision);
        $this->assertEquals('create', $revision->action);
        $this->assertEquals('1.0.0', $revision->version);
    }

    public function test_content_block_creates_revision_on_create(): void
    {
        $this->actingAs($this->user);

        $page = Page::factory()->create();
        $block = ContentBlock::factory()->create([
            'page_id' => $page->id,
            'type' => 'text', // This is a content block type, not a form element type
            'data' => ['content' => 'Test content'],
        ]);

        $this->assertTrue($block->hasRevisions());
        $this->assertEquals(1, $block->revision_count);

        $revision = $block->latestRevision();
        $this->assertNotNull($revision);
        $this->assertEquals('create', $revision->action);
        $this->assertEquals('1.0.0', $revision->version);
    }

    public function test_can_revert_to_previous_revision(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Original Name'],
        ]);

        $originalRevision = $form->latestRevision();

        $form->update(['name' => ['en' => 'Updated Name']]);
        $this->assertEquals('Updated Name', $form->fresh()->getTranslation('name', 'en'));

        $success = $form->revertToRevision($originalRevision);
        $this->assertTrue($success);

        $form->refresh();
        $this->assertEquals('Original Name', $form->getTranslation('name', 'en'));
        $this->assertEquals(3, $form->revision_count); // create + update + revert
    }

    public function test_revision_service_compare_revisions(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Original Name'],
        ]);

        $revision1 = $form->latestRevision();

        $form->update(['name' => ['en' => 'Updated Name']]);
        $revision2 = $form->latestRevision();

        $revisionService = app(RevisionService::class);
        $differences = $revisionService->compareRevisions($revision1, $revision2);

        $this->assertArrayHasKey('name', $differences);
        $this->assertEquals(['en' => 'Original Name'], $differences['name']['from']);
        $this->assertEquals(['en' => 'Updated Name'], $differences['name']['to']);
    }

    public function test_revision_metadata_includes_user_and_request_info(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $revision = $form->latestRevision();
        $this->assertEquals($this->user->id, $revision->user_id);
        $this->assertArrayHasKey('user_agent', $revision->metadata);
        $this->assertArrayHasKey('ip_address', $revision->metadata);
        $this->assertArrayHasKey('session_id', $revision->metadata);
    }

    public function test_revision_excludes_timestamp_fields(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $revision = $form->latestRevision();
        $this->assertArrayNotHasKey('created_at', $revision->data);
        $this->assertArrayNotHasKey('updated_at', $revision->data);
        $this->assertArrayNotHasKey('deleted_at', $revision->data);
    }

    public function test_revision_tracks_only_specified_fields(): void
    {
        $this->actingAs($this->user);

        $form = Form::factory()->create([
            'user_id' => $this->user->id,
            'name' => ['en' => 'Original Name'],
        ]);

        $form->update(['name' => ['en' => 'Updated Name']]);

        $revision = $form->latestRevision();
        $this->assertEquals('update', $revision->action);
        $this->assertArrayHasKey('name', $revision->changes);
    }

    public function test_testimonial_creates_revision_on_create(): void
    {
        $this->actingAs($this->user);

        $testimonial = Testimonial::factory()->create([
            'name' => 'John Doe',
            'content' => 'Great service!',
            'rating' => 5,
        ]);

        $this->assertTrue($testimonial->hasRevisions());
        $this->assertEquals(1, $testimonial->revision_count);

        $revision = $testimonial->latestRevision();
        $this->assertNotNull($revision);
        $this->assertEquals('create', $revision->action);
        $this->assertEquals('1.0.0', $revision->version);
    }

    public function test_testimonial_creates_revision_on_update(): void
    {
        $this->actingAs($this->user);

        $testimonial = Testimonial::factory()->create([
            'name' => 'John Doe',
            'content' => 'Original content',
        ]);

        $testimonial->update(['content' => 'Updated content']);

        $this->assertEquals(2, $testimonial->revision_count);

        $latestRevision = $testimonial->latestRevision();
        $this->assertEquals('update', $latestRevision->action);
        $this->assertEquals('1.0.1', $latestRevision->version);
        $this->assertArrayHasKey('content', $latestRevision->changes);
    }

    public function test_user_creates_revision_on_create(): void
    {
        $this->actingAs($this->user);

        $newUser = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertTrue($newUser->hasRevisions());
        $this->assertEquals(1, $newUser->revision_count);

        $revision = $newUser->latestRevision();
        $this->assertNotNull($revision);
        $this->assertEquals('create', $revision->action);
        $this->assertEquals('1.0.0', $revision->version);
    }

    public function test_user_creates_revision_on_update(): void
    {
        $this->actingAs($this->user);

        $newUser = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $newUser->update(['name' => 'Jane Smith']);

        $this->assertEquals(2, $newUser->revision_count);

        $latestRevision = $newUser->latestRevision();
        $this->assertEquals('update', $latestRevision->action);
        $this->assertEquals('1.0.1', $latestRevision->version);
        $this->assertArrayHasKey('name', $latestRevision->changes);
    }

    public function test_resource_revision_excludes_sensitive_fields(): void
    {
        $this->actingAs($this->user);

        $newUser = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $revision = $newUser->latestRevision();
        $this->assertArrayNotHasKey('password', $revision->data);
        $this->assertArrayNotHasKey('remember_token', $revision->data);
        $this->assertArrayNotHasKey('google_token', $revision->data);
        $this->assertArrayNotHasKey('facebook_token', $revision->data);
    }
}
