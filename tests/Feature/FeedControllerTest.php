<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_returns_rss_xml(): void
    {
        BlogPost::factory()->published()->create();

        $response = $this->get('/feed.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        $this->assertStringContainsString('<rss version="2.0"', $response->getContent());
    }

    public function test_feed_contains_published_posts(): void
    {
        $post = BlogPost::factory()->published()->create([
            'title' => 'Test RSS Post',
            'slug'  => 'test-rss-post',
        ]);

        $response = $this->get('/feed.xml');

        $response->assertOk();
        $this->assertStringContainsString('Test RSS Post', $response->getContent());
        $this->assertStringContainsString('test-rss-post', $response->getContent());
    }

    public function test_feed_excludes_draft_posts(): void
    {
        BlogPost::factory()->draft()->create(['title' => 'Draft Post']);
        BlogPost::factory()->published()->create(['title' => 'Published Post']);

        $response = $this->get('/feed.xml');

        $response->assertOk();
        $this->assertStringContainsString('Published Post', $response->getContent());
        $this->assertStringNotContainsString('Draft Post', $response->getContent());
    }

    public function test_feed_includes_category(): void
    {
        BlogPost::factory()->published()->create([
            'title'    => 'Laravel Tips',
            'category' => 'laravel',
        ]);

        $response = $this->get('/feed.xml');

        $this->assertStringContainsString('<category>laravel</category>', $response->getContent());
    }

    public function test_feed_returns_valid_xml_with_no_posts(): void
    {
        $response = $this->get('/feed.xml');

        $response->assertOk();
        $this->assertStringContainsString('<channel>', $response->getContent());
        $this->assertStringNotContainsString('<item>', $response->getContent());
    }
}
