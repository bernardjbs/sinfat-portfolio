<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    // GET /api/blog

    public function test_published_posts_appear_in_listing(): void
    {
        BlogPost::factory()->published()->count(3)->create();

        $this->getJson('/api/blog')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'title', 'slug', 'excerpt', 'category', 'published_at', 'ai_generated']],
                'meta' => ['current_page', 'last_page', 'total'],
            ]);
    }

    public function test_draft_posts_do_not_appear_in_listing(): void
    {
        BlogPost::factory()->draft()->count(2)->create();
        BlogPost::factory()->published()->count(1)->create();

        $this->getJson('/api/blog')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_listing_does_not_include_content_field(): void
    {
        BlogPost::factory()->published()->create();

        $response = $this->getJson('/api/blog')->assertOk();

        $this->assertArrayNotHasKey('content', $response->json('data.0'));
    }

    public function test_listing_does_not_include_admin_only_fields(): void
    {
        BlogPost::factory()->published()->create();

        $response = $this->getJson('/api/blog')->assertOk();
        $post = $response->json('data.0');

        $this->assertArrayNotHasKey('status', $post);
        $this->assertArrayNotHasKey('ai_model', $post);
    }

    public function test_listing_is_paginated(): void
    {
        BlogPost::factory()->published()->count(15)->create();

        $this->getJson('/api/blog')
            ->assertOk()
            ->assertJsonPath('meta.total', 15)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonCount(10, 'data');
    }

    // GET /api/blog/{slug}

    public function test_can_fetch_single_published_post_by_slug(): void
    {
        $post = BlogPost::factory()->published()->create();

        $this->getJson("/api/blog/{$post->slug}")
            ->assertOk()
            ->assertJsonPath('data.slug', $post->slug)
            ->assertJsonPath('data.title', $post->title);
    }

    public function test_single_post_includes_content_field(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->getJson("/api/blog/{$post->slug}")->assertOk();

        $this->assertArrayHasKey('content', $response->json('data'));
    }

    public function test_single_post_does_not_include_admin_only_fields(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->getJson("/api/blog/{$post->slug}")->assertOk();
        $data = $response->json('data');

        $this->assertArrayNotHasKey('status', $data);
        $this->assertArrayNotHasKey('ai_model', $data);
    }

    public function test_returns_404_for_non_existent_slug(): void
    {
        $this->getJson('/api/blog/does-not-exist')->assertNotFound();
    }

    public function test_returns_404_for_draft_post(): void
    {
        $post = BlogPost::factory()->draft()->create();

        $this->getJson("/api/blog/{$post->slug}")->assertNotFound();
    }

    public function test_single_post_content_is_rendered_as_html(): void
    {
        $post = BlogPost::factory()->published()->create([
            'content' => '## Hello World',
        ]);

        $response = $this->getJson("/api/blog/{$post->slug}")->assertOk();

        $this->assertStringContainsString('<h2>', $response->json('data.content'));
    }

    public function test_single_post_does_not_expose_raw_content_field(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->getJson("/api/blog/{$post->slug}")->assertOk();

        $this->assertArrayNotHasKey('raw_content', $response->json('data'));
    }

    // Reading time

    public function test_listing_includes_reading_time(): void
    {
        BlogPost::factory()->published()->create([
            'content' => implode(' ', array_fill(0, 400, 'word')),
        ]);

        $response = $this->getJson('/api/blog')->assertOk();

        $this->assertEquals(2, $response->json('data.0.reading_time'));
    }

    public function test_reading_time_is_at_least_one_minute(): void
    {
        BlogPost::factory()->published()->create([
            'content' => 'Short post.',
        ]);

        $response = $this->getJson('/api/blog')->assertOk();

        $this->assertEquals(1, $response->json('data.0.reading_time'));
    }
}
