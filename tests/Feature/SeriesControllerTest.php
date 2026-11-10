<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesControllerTest extends TestCase
{
    use RefreshDatabase;

    private function part(Series $series, int $part, int $daysAgo): BlogPost
    {
        return BlogPost::factory()->published()->inSeries($series, $part)
            ->create(['published_at' => now()->subDays($daysAgo)]);
    }

    // GET /api/series

    public function test_series_are_ordered_being_written_first_then_by_latest_part(): void
    {
        $writingOld = Series::factory()->create(['slug' => 'writing-old']);
        $writingNew = Series::factory()->create(['slug' => 'writing-new']);
        $completeOld = Series::factory()->complete()->create(['slug' => 'complete-old']);
        $completeMid = Series::factory()->complete()->create(['slug' => 'complete-mid']);
        $completeNew = Series::factory()->complete()->create(['slug' => 'complete-new']);

        $this->part($writingOld, 1, 10);
        $this->part($writingNew, 1, 2);
        $this->part($completeOld, 1, 20);
        $this->part($completeMid, 1, 6);
        $this->part($completeMid, 2, 5);
        $this->part($completeNew, 1, 1);

        // An unpublished part dated today must not count as the latest
        BlogPost::factory()->inSeries($completeOld, 2)->create(['status' => 'draft', 'published_at' => now()]);

        $this->getJson('/api/series')
            ->assertOk()
            ->assertJsonPath('data.*.slug', ['writing-new', 'writing-old', 'complete-new', 'complete-mid', 'complete-old']);
    }

    public function test_counts_and_last_updated_use_published_parts_only(): void
    {
        $series = Series::factory()->create();
        $latest = $this->part($series, 1, 3);
        $this->part($series, 2, 9);
        BlogPost::factory()->draft()->inSeries($series, 3)->create();

        $this->getJson('/api/series')
            ->assertOk()
            ->assertJsonPath('data.0.part_count', 2)
            ->assertJsonPath('data.0.last_updated', $latest->published_at->toISOString())
            ->assertJsonStructure(['data' => [['slug', 'title', 'pitch', 'status', 'part_count', 'last_updated']]]);
    }

    public function test_series_without_a_published_part_is_hidden(): void
    {
        $empty = Series::factory()->create(['slug' => 'only-drafts']);
        BlogPost::factory()->draft()->inSeries($empty, 1)->create();
        Series::factory()->create(['slug' => 'no-posts']);

        $this->getJson('/api/series')->assertOk()->assertJsonCount(0, 'data');
    }

    // GET /api/series/{slug}

    public function test_series_page_lists_published_parts_in_part_order(): void
    {
        $series = Series::factory()->complete()->create(['slug' => 'building']);
        // Part 2 published before part 1, as happened with the live posts
        $second = $this->part($series, 2, 9);
        $first = $this->part($series, 1, 3);
        BlogPost::factory()->draft()->inSeries($series, 3)->create();

        $this->getJson('/api/series/building')
            ->assertOk()
            ->assertJsonPath('data.slug', 'building')
            ->assertJsonPath('data.status', 'complete')
            ->assertJsonPath('data.part_count', 2)
            ->assertJsonPath('data.parts.*.slug', [$first->slug, $second->slug])
            ->assertJsonStructure(['data' => ['parts' => [['part', 'title', 'slug', 'excerpt', 'published_at']]]]);
    }

    public function test_unknown_series_returns_404(): void
    {
        $this->getJson('/api/series/does-not-exist')->assertNotFound();
    }

    public function test_series_with_only_drafts_returns_404(): void
    {
        $series = Series::factory()->create(['slug' => 'only-drafts']);
        BlogPost::factory()->draft()->inSeries($series, 1)->create();

        $this->getJson('/api/series/only-drafts')->assertNotFound();
    }
}
