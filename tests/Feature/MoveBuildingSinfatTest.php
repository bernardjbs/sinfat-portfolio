<?php

namespace Tests\Feature;

use App\Console\Commands\MoveBuildingSinfat;
use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClassConstant;
use Tests\TestCase;

class MoveBuildingSinfatTest extends TestCase
{
    use RefreshDatabase;

    private array $parts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parts = (new ReflectionClassConstant(MoveBuildingSinfat::class, 'PARTS'))->getValue();

        // The live posts as they stand before the move: old titles and excerpts, dates out of part order
        foreach (array_reverse($this->parts) as $i => $row) {
            BlogPost::factory()->published()->create([
                'slug'         => $row['slug'],
                'title'        => $row['title'][0],
                'excerpt'      => $row['excerpt'][0] ?? "Unchanged excerpt {$row['part']}.",
                'published_at' => now()->subDays(30 - $i),
            ]);
        }
    }

    private function snapshot(): array
    {
        return BlogPost::orderBy('id')->get(['slug', 'title', 'excerpt', 'content', 'status', 'published_at', 'series_id', 'series_part'])
            ->map(fn ($post) => $post->only(['slug', 'title', 'excerpt', 'content', 'status', 'series_id', 'series_part']) + ['published_at' => $post->published_at->toISOString()])
            ->all();
    }

    public function test_dry_run_writes_nothing(): void
    {
        $before = $this->snapshot();

        $this->artisan('blog:move-building-sinfat')->assertExitCode(0);

        $this->assertSame($before, $this->snapshot());
        $this->assertSame(0, Series::count());
    }

    public function test_apply_moves_the_14_posts_into_the_series(): void
    {
        $before = collect($this->snapshot())->keyBy('slug');

        $this->artisan('blog:move-building-sinfat', ['--apply' => true])->assertExitCode(0);

        $series = Series::where('slug', 'building-sinfat')->firstOrFail();
        $this->assertSame(['Building sinfat.com with AI', 'complete'], [$series->title, $series->status]);

        foreach ($this->parts as $row) {
            $post = BlogPost::where('slug', $row['slug'])->firstOrFail();
            $old = $before[$row['slug']];

            $this->assertSame([$series->id, $row['part'], $row['title'][1]], [$post->series_id, $post->series_part, $post->title]);
            $this->assertSame($row['excerpt'][1] ?? $old['excerpt'], $post->excerpt);
            $this->assertSame([$old['content'], $old['status'], $old['published_at']], [$post->content, $post->status, $post->published_at->toISOString()]);
        }

        $this->assertSame(14, BlogPost::count());
    }

    public function test_running_again_changes_nothing(): void
    {
        $this->artisan('blog:move-building-sinfat', ['--apply' => true])->assertExitCode(0);
        $after = $this->snapshot();

        $this->artisan('blog:move-building-sinfat', ['--apply' => true])
            ->expectsOutput('Nothing to do: already applied.')
            ->assertExitCode(0);

        $this->assertSame($after, $this->snapshot());
        $this->assertSame(1, Series::count());
    }

    public function test_refuses_when_a_post_is_missing(): void
    {
        BlogPost::where('slug', $this->parts[6]['slug'])->delete();
        $before = $this->snapshot();

        $this->artisan('blog:move-building-sinfat', ['--apply' => true])->assertExitCode(1);

        $this->assertSame($before, $this->snapshot());
        $this->assertSame(0, Series::count());
    }

    public function test_refuses_when_a_title_has_drifted(): void
    {
        BlogPost::where('slug', $this->parts[13]['slug'])->update(['title' => 'Edited since the ruling']);
        $before = $this->snapshot();

        $this->artisan('blog:move-building-sinfat', ['--apply' => true])->assertExitCode(1);

        $this->assertSame($before, $this->snapshot());
        $this->assertSame(0, Series::count());
    }

    public function test_refuses_when_an_excerpt_has_drifted(): void
    {
        BlogPost::where('slug', $this->parts[2]['slug'])->update(['excerpt' => 'Edited since the ruling']);
        $before = $this->snapshot();

        $this->artisan('blog:move-building-sinfat', ['--apply' => true])->assertExitCode(1);

        $this->assertSame($before, $this->snapshot());
    }
}
