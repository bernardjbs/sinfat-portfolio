<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        $admin = User::factory()->create();
        return $this->actingAs($admin, 'sanctum');
    }

    private function sitemap(): string
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringStartsWith('text/xml', $response->headers->get('Content-Type'));

        return $response->getContent();
    }

    // GET /sitemap.xml

    public function test_no_sitemap_file_shadows_the_route(): void
    {
        // The web server serves public/ files before Laravel, so a leftover file would go stale unseen
        $this->assertFileDoesNotExist(public_path('sitemap.xml'));
    }

    public function test_sitemap_lists_the_site_pages(): void
    {
        $xml = $this->sitemap();

        $this->assertStringContainsString('<urlset', $xml);
        foreach (['/about', '/projects', '/blog', '/blog/series', '/uses', '/contact'] as $page) {
            $this->assertStringContainsString("{$page}</loc>", $xml);
        }
    }

    public function test_sitemap_lists_published_posts_and_not_drafts(): void
    {
        BlogPost::factory()->published()->create(['slug' => 'a-published-post']);
        BlogPost::factory()->draft()->create(['slug' => 'a-draft-post']);

        $xml = $this->sitemap();

        $this->assertStringContainsString('/blog/a-published-post</loc>', $xml);
        $this->assertStringNotContainsString('a-draft-post', $xml);
    }

    public function test_sitemap_lists_each_public_series_and_not_a_series_of_drafts(): void
    {
        $public = Series::factory()->create(['slug' => 'building']);
        BlogPost::factory()->published()->inSeries($public, 1)->create();
        $empty = Series::factory()->create(['slug' => 'only-drafts']);
        BlogPost::factory()->draft()->inSeries($empty, 1)->create();

        $xml = $this->sitemap();

        $this->assertStringContainsString('/blog/series/building</loc>', $xml);
        $this->assertStringNotContainsString('/blog/series/only-drafts', $xml);
    }

    // Every way a post is published or unpublished shows in the sitemap at once, with nothing to rebuild

    public function test_a_post_published_from_the_editor_save_is_listed_at_once(): void
    {
        $post = BlogPost::factory()->draft()->create(['slug' => 'saved-as-published']);

        $this->actingAsAdmin()->putJson("/api/admin/blog/{$post->id}", [
            'title'   => $post->title,
            'content' => $post->content,
            'status'  => 'published',
        ])->assertOk();

        $this->assertStringContainsString('/blog/saved-as-published</loc>', $this->sitemap());
    }

    public function test_a_post_toggled_off_and_on_leaves_and_rejoins_the_sitemap(): void
    {
        $post = BlogPost::factory()->published()->create(['slug' => 'toggled-post']);

        $this->actingAsAdmin()->patchJson("/api/admin/blog/{$post->id}/publish")->assertOk();
        $this->assertStringNotContainsString('toggled-post', $this->sitemap());

        $this->actingAsAdmin()->patchJson("/api/admin/blog/{$post->id}/publish")->assertOk();
        $this->assertStringContainsString('/blog/toggled-post</loc>', $this->sitemap());
    }
}
