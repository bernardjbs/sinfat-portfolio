<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlogControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        $admin = User::factory()->create();
        return $this->actingAs($admin, 'sanctum');
    }

    // Auth guard

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/admin/blog')->assertUnauthorized();
    }

    // GET /api/admin/blog

    public function test_admin_can_list_all_posts_including_drafts(): void
    {
        BlogPost::factory()->published()->count(2)->create();
        BlogPost::factory()->draft()->count(3)->create();

        $this->actingAsAdmin()
            ->getJson('/api/admin/blog')
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_admin_listing_includes_status_field(): void
    {
        BlogPost::factory()->published()->create();

        $response = $this->actingAsAdmin()
            ->getJson('/api/admin/blog')
            ->assertOk();

        $this->assertArrayHasKey('status', $response->json('data.0'));
    }

    // POST /api/admin/blog

    public function test_admin_can_create_post(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/blog', [
                'title'   => 'My Test Post',
                'content' => '## Hello World',
                'status'  => 'draft',
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'My Test Post')
            ->assertJsonPath('data.slug', 'my-test-post')
            ->assertJsonPath('data.status', 'draft');

        $this->assertDatabaseHas('blog_posts', ['title' => 'My Test Post']);
    }

    public function test_create_post_requires_title_and_content(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/blog', ['status' => 'draft'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_create_post_auto_generates_slug(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/blog', [
                'title'   => 'Auto Slug Test',
                'content' => 'Content here',
                'status'  => 'draft',
            ])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'auto-slug-test');
    }

    // GET /api/admin/blog/{id}

    public function test_admin_can_fetch_single_post(): void
    {
        $post = BlogPost::factory()->create();

        $this->actingAsAdmin()
            ->getJson("/api/admin/blog/{$post->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_admin_single_post_returns_raw_content_not_rendered(): void
    {
        $post = BlogPost::factory()->create(['content' => '## Hello World']);

        $response = $this->actingAsAdmin()
            ->getJson("/api/admin/blog/{$post->id}")
            ->assertOk();

        $this->assertArrayHasKey('raw_content', $response->json('data'));
        $this->assertSame('## Hello World', $response->json('data.raw_content'));
        $this->assertArrayNotHasKey('content', $response->json('data'));
    }

    public function test_fetch_single_post_returns_404_for_missing(): void
    {
        $this->actingAsAdmin()
            ->getJson('/api/admin/blog/999')
            ->assertNotFound();
    }

    // PUT /api/admin/blog/{id}

    public function test_admin_can_update_post(): void
    {
        $post = BlogPost::factory()->create(['title' => 'Old Title']);

        $this->actingAsAdmin()
            ->putJson("/api/admin/blog/{$post->id}", [
                'title'   => 'New Title',
                'content' => 'Updated content',
                'status'  => 'draft',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'New Title');

        $this->assertDatabaseHas('blog_posts', ['title' => 'New Title']);
    }

    // DELETE /api/admin/blog/{id}

    public function test_admin_can_delete_post(): void
    {
        $post = BlogPost::factory()->create();

        $this->actingAsAdmin()
            ->deleteJson("/api/admin/blog/{$post->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Post deleted');

        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);
    }

    // PATCH /api/admin/blog/{id}/publish

    public function test_admin_can_publish_draft_post(): void
    {
        $post = BlogPost::factory()->draft()->create();

        $this->actingAsAdmin()
            ->patchJson("/api/admin/blog/{$post->id}/publish")
            ->assertOk()
            ->assertJsonPath('data.status', 'published');

        $this->assertDatabaseHas('blog_posts', [
            'id'     => $post->id,
            'status' => 'published',
        ]);
    }

    public function test_admin_can_unpublish_published_post(): void
    {
        $post = BlogPost::factory()->published()->create();

        $this->actingAsAdmin()
            ->patchJson("/api/admin/blog/{$post->id}/publish")
            ->assertOk()
            ->assertJsonPath('data.status', 'draft');
    }
}
