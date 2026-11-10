<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesPageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // GET /blog/series

    public function test_series_index_page_carries_its_own_meta(): void
    {
        $html = $this->get('/blog/series')->assertOk()->getContent();

        $this->assertStringContainsString('<title>Series · sinfat.com</title>', $html);
        $this->assertStringContainsString('<meta property="og:url" content="'.config('app.url').'/blog/series">', $html);
        $this->assertStringContainsString('what worked, what broke, and where the AI got it wrong.', $html);
    }

    public function test_series_index_is_not_taken_for_a_post_slug(): void
    {
        BlogPost::factory()->published()->create(['slug' => 'series', 'title' => 'A Post Called Series']);

        $html = $this->get('/blog/series')->assertOk()->getContent();

        $this->assertStringNotContainsString('A Post Called Series', $html);
    }

    // GET /blog/series/{slug}

    public function test_public_series_page_carries_the_series_meta(): void
    {
        $series = Series::factory()->create([
            'slug'  => 'building',
            'title' => 'Building sinfat.com with AI',
            'pitch' => 'From a plan to production.',
        ]);
        BlogPost::factory()->published()->inSeries($series, 1)->create();

        $html = $this->get('/blog/series/building')->assertOk()->getContent();

        $this->assertStringContainsString('<title>Building sinfat.com with AI · sinfat.com</title>', $html);
        $this->assertStringContainsString('<meta name="description" content="From a plan to production.">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="'.config('app.url').'/blog/series/building">', $html);
    }

    public function test_unknown_series_returns_404_with_the_app_shell(): void
    {
        $this->get('/blog/series/does-not-exist')
            ->assertNotFound()
            ->assertSee('<div id="app">', false);
    }

    public function test_series_with_only_drafts_returns_404_and_hides_its_title(): void
    {
        $series = Series::factory()->create(['slug' => 'secret', 'title' => 'Unannounced Series']);
        BlogPost::factory()->draft()->inSeries($series, 1)->create();

        $html = $this->get('/blog/series/secret')->assertNotFound()->getContent();

        $this->assertStringNotContainsString('Unannounced Series', $html);
    }
}
