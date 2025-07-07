<?php

declare(strict_types=1);

use App\Http\Resources\Admin\TestimonialResource;
use App\Models\Testimonial;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Testimonial Resource Revision System', function () {
    it('creates a published revision when creating a new testimonial', function () {
        $resource = new TestimonialResource;

        Livewire::test(\App\Livewire\ResourceSystem\ResourceForm::class, [
            'resource' => $resource,
        ])
            ->set('data.name', 'John Doe')
            ->set('data.title', 'CEO')
            ->set('data.content', 'Great product!')
            ->set('data.rating', 5)
            ->set('data.source', 'Google Reviews')
            ->set('data.order', 1)
            ->call('save');

        $testimonial = Testimonial::where('name', 'John Doe')->first();
        $this->assertNotNull($testimonial);

        // Check that a revision was created
        $this->assertDatabaseCount('revisions', 1);
        $revision = $testimonial->latestRevision();
        $this->assertNotNull($revision);
        $this->assertTrue($revision->is_published);
        $this->assertEquals('John Doe', $revision->data['name']);
    });

    it('creates a draft revision when updating an existing testimonial', function () {
        // Create a testimonial with initial data
        $testimonial = Testimonial::factory()->create([
            'name' => 'Original Name',
            'content' => 'Original content',
        ]);

        $resource = new TestimonialResource;

        Livewire::test(\App\Livewire\ResourceSystem\ResourceForm::class, [
            'resource' => $resource,
            'resourceId' => $testimonial->id,
        ])
            ->set('data.name', 'Updated Name')
            ->set('data.content', 'Updated content')
            ->call('save');

        $testimonial->refresh();

        // Check that a new draft revision was created
        $this->assertDatabaseCount('revisions', 2); // 1 from creation + 1 from update
        $latestRevision = $testimonial->latestRevision();
        $this->assertFalse($latestRevision->is_published);
        $this->assertEquals('Updated Name', $latestRevision->data['name']);

        // The main model should still have the original data
        $this->assertEquals('Original Name', $testimonial->name);
    });

    it('can publish a draft revision', function () {
        // Create a testimonial with initial data
        $testimonial = Testimonial::factory()->create([
            'name' => 'Original Name',
        ]);

        $resource = new TestimonialResource;

        Livewire::test(\App\Livewire\ResourceSystem\ResourceForm::class, [
            'resource' => $resource,
            'resourceId' => $testimonial->id,
        ])
            ->set('data.name', 'Updated Name')
            ->call('publish');

        $testimonial->refresh();

        // Check that a new published revision was created
        $this->assertDatabaseCount('revisions', 2); // 1 from creation + 1 from publish
        $latestRevision = $testimonial->latestRevision();
        $this->assertTrue($latestRevision->is_published);
        $this->assertEquals('Updated Name', $latestRevision->data['name']);

        // The main model should now have the updated data
        $this->assertEquals('Updated Name', $testimonial->name);
    });

    it('detects unsaved changes correctly', function () {
        // Create a testimonial with initial data
        $testimonial = Testimonial::factory()->create([
            'name' => 'Original Name',
        ]);

        $resource = new TestimonialResource;

        $component = Livewire::test(\App\Livewire\ResourceSystem\ResourceForm::class, [
            'resource' => $resource,
            'resourceId' => $testimonial->id,
        ]);

        // Initially no changes
        $this->assertFalse($component->get('hasUnsavedChanges'));

        // Make changes
        $component->set('data.name', 'Updated Name');
        $this->assertTrue($component->get('hasUnsavedChanges'));

        // Save changes
        $component->call('save');
        $this->assertFalse($component->get('hasUnsavedChanges'));
    });
});
