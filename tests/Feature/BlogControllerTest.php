<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
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

    // Series

    private function seriesWithParts(string $status = 'complete'): array
    {
        $series = Series::factory()->create(['slug' => 'building', 'status' => $status]);

        // Dates out of part order, and a standalone post published between them
        $parts = [
            1 => BlogPost::factory()->published()->inSeries($series, 1)->create(['published_at' => now()->subDays(8)]),
            2 => BlogPost::factory()->published()->inSeries($series, 2)->create(['published_at' => now()->subDays(9)]),
            3 => BlogPost::factory()->published()->inSeries($series, 3)->create(['published_at' => now()->subDays(6)]),
        ];
        BlogPost::factory()->draft()->inSeries($series, 4)->create();
        BlogPost::factory()->published()->create(['published_at' => now()->subDays(7)]);
        BlogPost::factory()->published()->create(['published_at' => now()->subDay()]);

        return [$series, $parts];
    }

    public function test_series_posts_carry_their_part_and_published_total(): void
    {
        [, $parts] = $this->seriesWithParts();

        $listed = collect($this->getJson('/api/blog')->assertOk()->json('data'))->firstWhere('slug', $parts[2]->slug);

        $this->assertSame(['slug' => 'building', 'title' => $parts[2]->series->title, 'status' => 'complete', 'part' => 2, 'total' => 3], $listed['series']);

        $this->getJson("/api/blog/{$parts[2]->slug}")
            ->assertOk()
            ->assertJsonPath('data.series.part', 2)
            ->assertJsonPath('data.series.total', 3);
    }

    public function test_first_part_has_no_previous_and_next_stays_in_the_series(): void
    {
        [, $parts] = $this->seriesWithParts();

        $this->getJson("/api/blog/{$parts[1]->slug}")
            ->assertOk()
            ->assertJsonPath('previous', null)
            ->assertJsonPath('next.slug', $parts[2]->slug)
            ->assertJsonPath('next.part', 2);
    }

    public function test_last_published_part_has_no_next_and_previous_stays_in_the_series(): void
    {
        [, $parts] = $this->seriesWithParts();

        $this->getJson("/api/blog/{$parts[3]->slug}")
            ->assertOk()
            ->assertJsonPath('next', null)
            ->assertJsonPath('previous.slug', $parts[2]->slug);
    }

    public function test_series_post_lists_its_published_parts_in_order(): void
    {
        [, $parts] = $this->seriesWithParts();

        $this->getJson("/api/blog/{$parts[2]->slug}")
            ->assertOk()
            ->assertJsonPath('series_parts.*.slug', [$parts[1]->slug, $parts[2]->slug, $parts[3]->slug])
            ->assertJsonPath('series_parts.*.part', [1, 2, 3]);
    }

    public function test_standalone_post_keeps_date_order_and_has_no_series(): void
    {
        $older = BlogPost::factory()->published()->create(['published_at' => now()->subDays(3)]);
        $post = BlogPost::factory()->published()->create(['published_at' => now()->subDays(2)]);
        $newer = BlogPost::factory()->published()->create(['published_at' => now()->subDay()]);

        $this->getJson("/api/blog/{$post->slug}")
            ->assertOk()
            ->assertJsonPath('data.series', null)
            ->assertJsonPath('series_parts', null)
            ->assertJsonPath('previous', ['title' => $older->title, 'slug' => $older->slug])
            ->assertJsonPath('next', ['title' => $newer->title, 'slug' => $newer->slug]);

        $listed = collect($this->getJson('/api/blog')->json('data'))->firstWhere('slug', $post->slug);
        $this->assertArrayHasKey('series', $listed);
        $this->assertNull($listed['series']);
    }

    public function test_notebook_links_open_in_a_new_tab_and_other_links_do_not(): void
    {
        $post = BlogPost::factory()->published()->create([
            'content' => "See [the plan](/notebook/2026-11-09-2327/) and [another post](/blog/other).",
        ]);

        $html = $this->getJson("/api/blog/{$post->slug}")->assertOk()->json('data.content');

        preg_match('#<a [^>]*href="/notebook/2026-11-09-2327/"[^>]*>#', $html, $notebook);
        preg_match('#<a [^>]*href="/blog/other"[^>]*>#', $html, $other);

        $this->assertNotEmpty($notebook, $html);
        $this->assertStringContainsString('target="_blank"', $notebook[0]);
        $this->assertStringContainsString('rel="noopener"', $notebook[0]);
        $this->assertNotEmpty($other, $html);
        $this->assertStringNotContainsString('target=', $other[0]);
    }
}
