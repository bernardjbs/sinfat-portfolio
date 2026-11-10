<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // GET /blog/{slug}

    public function test_published_post_page_carries_the_post_meta(): void
    {
        $post = BlogPost::factory()->published()->create([
            'title'   => 'Honest URLs for a Vue SPA',
            'slug'    => 'honest-urls',
            'excerpt' => 'Per-post previews without server rendering.',
        ]);

        $html = $this->get("/blog/{$post->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('<title>Honest URLs for a Vue SPA · sinfat.com</title>', $html);
        $this->assertStringContainsString('<meta name="description" content="Per-post previews without server rendering.">', $html);
        $this->assertStringContainsString('<meta property="og:title" content="Honest URLs for a Vue SPA">', $html);
        $this->assertStringContainsString('<meta property="og:description" content="Per-post previews without server rendering.">', $html);
        $this->assertStringContainsString('<meta property="og:type" content="article">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="'.config('app.url').'/blog/honest-urls">', $html);
        $this->assertStringContainsString('<div id="app"></div>', $html);
    }

    public function test_post_without_excerpt_falls_back_to_site_description(): void
    {
        $post = BlogPost::factory()->published()->create(['excerpt' => null]);

        $html = $this->get("/blog/{$post->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('<meta name="description" content="Bernard — full-stack developer (Laravel + Vue) based in Perth, Australia.">', $html);
    }

    public function test_missing_post_page_returns_404_with_app_shell(): void
    {
        $response = $this->get('/blog/does-not-exist')->assertNotFound();

        $this->assertStringContainsString('<div id="app"></div>', $response->getContent());
        $this->assertStringContainsString('<title>sinfat.com</title>', $response->getContent());
    }

    public function test_draft_post_page_returns_404_and_never_leaks_the_draft(): void
    {
        $draft = BlogPost::factory()->draft()->create([
            'title'   => 'Unreleased Draft Title',
            'excerpt' => 'Unreleased draft excerpt.',
        ]);

        $html = $this->get("/blog/{$draft->slug}")->assertNotFound()->getContent();

        $this->assertStringNotContainsString('Unreleased Draft Title', $html);
        $this->assertStringNotContainsString('Unreleased draft excerpt.', $html);
    }

    public function test_post_title_is_html_escaped_in_meta(): void
    {
        $post = BlogPost::factory()->published()->create([
            'title' => 'Break "out" <script>alert(1)</script>',
            'slug'  => 'escaped-title',
        ]);

        $html = $this->get("/blog/{$post->slug}")->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('content="Break &quot;out&quot; &lt;script&gt;alert(1)&lt;/script&gt;"', $html);
    }

    // Every other route keeps the default shell

    public function test_other_routes_keep_the_default_meta(): void
    {
        $html = $this->get('/about')->assertOk()->getContent();

        $this->assertStringContainsString('<title>sinfat.com</title>', $html);
        $this->assertStringContainsString('<meta property="og:type" content="website">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="https://sinfat.com">', $html);
    }
}
