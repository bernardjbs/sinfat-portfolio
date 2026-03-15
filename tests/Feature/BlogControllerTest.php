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

    // Search

    public function test_search_filters_by_title(): void
    {
        BlogPost::factory()->published()->create(['title' => 'Building with Laravel']);
        BlogPost::factory()->published()->create(['title' => 'Vue SPA Guide']);

        $this->getJson('/api/blog?search=Laravel')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Building with Laravel');
    }

    public function test_search_filters_by_excerpt(): void
    {
        BlogPost::factory()->published()->create(['title' => 'Post A', 'excerpt' => 'About streaming SSE']);
        BlogPost::factory()->published()->create(['title' => 'Post B', 'excerpt' => 'About databases']);

        $this->getJson('/api/blog?search=streaming')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Post A');
    }

    public function test_search_returns_empty_when_no_match(): void
    {
        BlogPost::factory()->published()->count(3)->create();

        $this->getJson('/api/blog?search=zzzznotfound')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    // Sort

    public function test_sort_oldest_returns_oldest_first(): void
    {
        $old = BlogPost::factory()->published()->create(['published_at' => now()->subDays(10)]);
        $new = BlogPost::factory()->published()->create(['published_at' => now()->subDays(1)]);

        $response = $this->getJson('/api/blog?sort=oldest')->assertOk();

        $this->assertEquals($old->slug, $response->json('data.0.slug'));
        $this->assertEquals($new->slug, $response->json('data.1.slug'));
    }

    public function test_sort_newest_is_default(): void
    {
        $old = BlogPost::factory()->published()->create(['published_at' => now()->subDays(10)]);
        $new = BlogPost::factory()->published()->create(['published_at' => now()->subDays(1)]);

        $response = $this->getJson('/api/blog')->assertOk();

        $this->assertEquals($new->slug, $response->json('data.0.slug'));
        $this->assertEquals($old->slug, $response->json('data.1.slug'));
    }

    // Next / Previous

    public function test_show_includes_next_and_previous_posts(): void
    {
        $first = BlogPost::factory()->published()->create(['published_at' => now()->subDays(3)]);
        $middle = BlogPost::factory()->published()->create(['published_at' => now()->subDays(2)]);
        $last = BlogPost::factory()->published()->create(['published_at' => now()->subDays(1)]);

        $response = $this->getJson("/api/blog/{$middle->slug}")->assertOk();

        $this->assertEquals($last->slug, $response->json('next.slug'));
        $this->assertEquals($first->slug, $response->json('previous.slug'));
    }

    public function test_first_post_has_no_previous(): void
    {
        $first = BlogPost::factory()->published()->create(['published_at' => now()->subDays(3)]);
        BlogPost::factory()->published()->create(['published_at' => now()->subDays(1)]);

        $response = $this->getJson("/api/blog/{$first->slug}")->assertOk();

        $this->assertNull($response->json('previous'));
        $this->assertNotNull($response->json('next'));
    }

    public function test_last_post_has_no_next(): void
    {
        BlogPost::factory()->published()->create(['published_at' => now()->subDays(3)]);
        $last = BlogPost::factory()->published()->create(['published_at' => now()->subDays(1)]);

        $response = $this->getJson("/api/blog/{$last->slug}")->assertOk();

        $this->assertNotNull($response->json('previous'));
        $this->assertNull($response->json('next'));
    }

    public function test_next_previous_excludes_draft_posts(): void
    {
        $first = BlogPost::factory()->published()->create(['published_at' => now()->subDays(3)]);
        BlogPost::factory()->draft()->create(['published_at' => now()->subDays(2)]);
        $last = BlogPost::factory()->published()->create(['published_at' => now()->subDays(1)]);

        $response = $this->getJson("/api/blog/{$first->slug}")->assertOk();

        $this->assertEquals($last->slug, $response->json('next.slug'));
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
