<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_the_series_index_and_each_public_series(): void
    {
        $public = Series::factory()->create(['slug' => 'building']);
        BlogPost::factory()->published()->inSeries($public, 1)->create();
        $empty = Series::factory()->create(['slug' => 'only-drafts']);
        BlogPost::factory()->draft()->inSeries($empty, 1)->create();

        $path = config('app.sitemap_path');
        $original = is_file($path) ? file_get_contents($path) : null;

        try {
            $this->artisan('sitemap:generate')->assertExitCode(0);
            $xml = file_get_contents($path);
        } finally {
            $original === null ? @unlink($path) : file_put_contents($path, $original);
        }

        $this->assertStringContainsString('/blog/series</loc>', $xml);
        $this->assertStringContainsString('/blog/series/building</loc>', $xml);
        $this->assertStringNotContainsString('/blog/series/only-drafts', $xml);
    }
}
