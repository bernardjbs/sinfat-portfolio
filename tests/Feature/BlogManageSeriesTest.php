<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogManageSeriesTest extends TestCase
{
    use RefreshDatabase;

    private string $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = tempnam(sys_get_temp_dir(), 'post');
        file_put_contents($this->file, str_repeat('A paragraph of post content. ', 10));
    }

    protected function tearDown(): void
    {
        @unlink($this->file);

        parent::tearDown();
    }

    private function seriesWithParts(int $count): Series
    {
        $series = Series::factory()->create(['slug' => 'building']);

        foreach (range(1, $count) as $part) {
            BlogPost::factory()->published()->inSeries($series, $part)->create(['slug' => "part-{$part}"]);
        }

        return $series;
    }

    private function parts(Series $series): array
    {
        return BlogPost::where('series_id', $series->id)->orderBy('series_part')->pluck('series_part', 'slug')->all();
    }

    public function test_series_create_update_and_complete(): void
    {
        $this->artisan('blog:manage', ['action' => 'series', 'subaction' => 'create', '--slug' => 'notes', '--title' => 'Notes', '--pitch' => 'Short notes.'])
            ->assertExitCode(0);
        $this->assertDatabaseHas('series', ['slug' => 'notes', 'title' => 'Notes', 'pitch' => 'Short notes.', 'status' => 'writing']);

        $this->artisan('blog:manage', ['action' => 'series', 'subaction' => 'update', '--slug' => 'notes', '--pitch' => 'Longer notes.'])
            ->assertExitCode(0);
        $this->assertDatabaseHas('series', ['slug' => 'notes', 'title' => 'Notes', 'pitch' => 'Longer notes.']);

        $this->artisan('blog:manage', ['action' => 'series', 'subaction' => 'complete', '--slug' => 'notes'])
            ->assertExitCode(0);
        $this->assertDatabaseHas('series', ['slug' => 'notes', 'status' => 'complete']);
    }

    public function test_series_create_refuses_a_taken_slug(): void
    {
        Series::factory()->create(['slug' => 'notes', 'title' => 'Original']);

        $this->artisan('blog:manage', ['action' => 'series', 'subaction' => 'create', '--slug' => 'notes', '--title' => 'Copy'])
            ->assertExitCode(1);
        $this->assertDatabaseHas('series', ['slug' => 'notes', 'title' => 'Original']);
    }

    public function test_post_create_with_series_appends_a_part(): void
    {
        $series = $this->seriesWithParts(2);

        $this->artisan('blog:manage', ['action' => 'create', '--title' => 'Third Part', '--file' => $this->file, '--series' => 'building'])
            ->assertExitCode(0);

        $this->assertSame(['part-1' => 1, 'part-2' => 2, 'third-part' => 3], $this->parts($series));
    }

    public function test_post_update_into_a_taken_part_moves_later_parts_up(): void
    {
        $series = $this->seriesWithParts(3);
        $post = BlogPost::factory()->published()->create(['slug' => 'inserted']);

        $this->artisan('blog:manage', ['action' => 'update', '--id' => $post->id, '--series' => 'building', '--part' => '2', '--force' => true])
            ->assertExitCode(0);

        $this->assertSame(['part-1' => 1, 'inserted' => 2, 'part-2' => 3, 'part-3' => 4], $this->parts($series));
    }

    public function test_post_update_into_a_free_part_moves_nothing(): void
    {
        // Parts 1 and 3, with 2 free: filling the gap leaves part 3 where it is
        $series = $this->seriesWithParts(3);
        BlogPost::where('slug', 'part-2')->update(['series_id' => null, 'series_part' => null]);
        $post = BlogPost::factory()->published()->create(['slug' => 'gap']);

        $this->artisan('blog:manage', ['action' => 'update', '--id' => $post->id, '--series' => 'building', '--part' => '2', '--force' => true])
            ->assertExitCode(0);

        $this->assertSame(['part-1' => 1, 'gap' => 2, 'part-3' => 3], $this->parts($series));
    }

    public function test_unknown_series_leaves_the_post_untouched(): void
    {
        $post = BlogPost::factory()->published()->create();

        $this->artisan('blog:manage', ['action' => 'update', '--id' => $post->id, '--series' => 'missing', '--force' => true])
            ->assertExitCode(1);

        $this->assertNull($post->fresh()->series_id);
    }

    public function test_part_must_be_a_whole_number(): void
    {
        $this->seriesWithParts(1);
        $post = BlogPost::factory()->published()->create();

        $this->artisan('blog:manage', ['action' => 'update', '--id' => $post->id, '--series' => 'building', '--part' => '0', '--force' => true])
            ->assertExitCode(1);

        $this->assertNull($post->fresh()->series_id);
    }
}
