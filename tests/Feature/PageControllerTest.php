<?php

namespace Tests\Feature;

use App\Enums\PublishStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_view_published_page_publicly()
    {
        $page = Page::factory()->create([
            'status' => PublishStatus::PUBLISHED,
            'slug' => 'test-page',
        ]);

        $response = $this->get('/test-page');

        $response->assertStatus(200);
        $response->assertViewIs('pages.show');
        $response->assertViewHas('page', $page);
    }

    /** @test */
    public function it_cannot_view_draft_page_publicly()
    {
        $page = Page::factory()->create([
            'status' => PublishStatus::DRAFT,
            'slug' => 'draft-page',
        ]);

        $response = $this->get('/draft-page');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_view_draft_page_with_proper_permissions()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('pages.view');

        $page = Page::factory()->create([
            'status' => PublishStatus::DRAFT,
            'slug' => 'draft-page',
        ]);

        $response = $this->actingAs($user)->get('/draft-page');

        $response->assertStatus(200);
        $response->assertViewIs('pages.show');
        $response->assertViewHas('page', $page);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_page()
    {
        $response = $this->get('/nonexistent-page');

        $response->assertStatus(404);
    }
} 