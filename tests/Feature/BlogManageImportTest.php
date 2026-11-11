<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogManageImportTest extends TestCase
{
    use RefreshDatabase;

    private string $body;

    /** @var list<string> */
    private array $cleanup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->body = str_repeat('A paragraph of post content. ', 10);
    }

    protected function tearDown(): void
    {
        foreach ($this->cleanup as $path) {
            @unlink($path);
        }

        foreach (glob(storage_path('app/blog-exports/backups/import-test-*.md')) as $backup) {
            @unlink($backup);
        }

        parent::tearDown();
    }

    private function exportFile(BlogPost $post): string
    {
        $this->artisan('blog:manage', ['action' => 'export', '--id' => $post->id])->assertExitCode(0);

        return $this->cleanup[] = storage_path("app/blog-exports/{$post->slug}.md");
    }

    private function writeFile(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'post');
        file_put_contents($path, $contents);

        return $this->cleanup[] = $path;
    }

    private function header(array $meta): string
    {
        $lines = array_map(fn ($key, $value) => "{$key}: " . json_encode($value), array_keys($meta), $meta);

        return "---\n" . implode("\n", $lines) . "\n---\n";
    }

    public function test_export_writes_the_post_details_above_the_content(): void
    {
        $series = Series::factory()->create(['slug' => 'factory']);
        $post = BlogPost::factory()->inSeries($series, 2)->create([
            'slug' => 'import-test-export', 'title' => 'A "quoted" title', 'excerpt' => 'One line.',
            'content' => $this->body, 'ai_model' => 'claude-opus-5-5',
        ]);

        $file = file_get_contents($this->exportFile($post));

        $this->assertStringStartsWith("---\nslug: \"import-test-export\"\ntitle: \"A \\\"quoted\\\" title\"\n", $file);
        $this->assertStringContainsString("series: \"factory\"\npart: 2\n", $file);
        $this->assertStringContainsString("ai_model: \"claude-opus-5-5\"\n", $file);
        $this->assertStringEndsWith("---\n" . $this->body, $file);
    }

    public function test_update_from_an_exported_file_stores_only_the_content(): void
    {
        $post = BlogPost::factory()->create(['slug' => 'import-test-update', 'content' => $this->body]);
        $file = $this->exportFile($post);
        file_put_contents($file, str_replace('A paragraph', 'An edited paragraph', file_get_contents($file)));

        $this->artisan('blog:manage', ['action' => 'update', '--id' => $post->id, '--file' => $file, '--force' => true])
            ->assertExitCode(0);

        $content = $post->fresh()->content;
        $this->assertStringStartsWith('An edited paragraph', $content);
        $this->assertStringNotContainsString('slug:', $content);
    }

    public function test_create_from_a_file_with_a_header_stores_only_the_content(): void
    {
        $file = $this->writeFile($this->header(['slug' => 'ignored', 'title' => 'Ignored']) . $this->body);

        $this->artisan('blog:manage', ['action' => 'create', '--title' => 'Import test create', '--file' => $file])
            ->assertExitCode(0);

        $this->assertSame($this->body, BlogPost::where('title', 'Import test create')->value('content'));
    }

    public function test_import_creates_a_draft_from_an_export(): void
    {
        $series = Series::factory()->create(['slug' => 'factory']);
        $post = BlogPost::factory()->published()->inSeries($series, 1)->create([
            'slug' => 'import-test-new', 'title' => 'New post', 'excerpt' => 'Its excerpt.',
            'content' => $this->body, 'ai_generated' => true, 'ai_model' => 'claude-opus-5-5',
        ]);
        $file = $this->exportFile($post);
        $post->delete();

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file])->assertExitCode(0);

        $created = BlogPost::where('slug', 'import-test-new')->firstOrFail();
        $this->assertSame(
            ['New post', 'Its excerpt.', $this->body, 'draft', null, 'claude-opus-5-5', true, $series->id, 1],
            [$created->title, $created->excerpt, $created->content, $created->status, $created->published_at,
             $created->ai_model, $created->ai_generated, $created->series_id, $created->series_part],
        );
    }

    public function test_import_updates_a_post_with_the_same_slug_and_keeps_a_backup(): void
    {
        $post = BlogPost::factory()->published()->create([
            'slug' => 'import-test-existing', 'title' => 'Old title', 'content' => $this->body,
        ]);
        $newBody = str_replace('A paragraph', 'A rewritten paragraph', $this->body);
        $file = $this->writeFile($this->header(['slug' => 'import-test-existing', 'title' => 'New title']) . $newBody);

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file])->assertExitCode(0);

        $post->refresh();
        $this->assertSame(['New title', $newBody, 'published'], [$post->title, $post->content, $post->status]);
        $this->assertSame(1, BlogPost::where('slug', 'import-test-existing')->count());

        $backups = glob(storage_path('app/blog-exports/backups/import-test-existing-*.md'));
        $this->assertCount(1, $backups);
        $this->assertStringEndsWith("---\n" . $this->body, file_get_contents($backups[0]));
    }

    public function test_import_refuses_a_series_that_does_not_exist_here(): void
    {
        $file = $this->writeFile($this->header(['slug' => 'import-test-orphan', 'title' => 'Orphan', 'series' => 'missing', 'part' => 1]) . $this->body);

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file])
            ->expectsOutputToContain('Series not found here: missing')
            ->assertExitCode(1);

        $this->assertFalse(BlogPost::where('slug', 'import-test-orphan')->exists());
    }

    public function test_import_refuses_a_part_held_by_another_post(): void
    {
        $series = Series::factory()->create(['slug' => 'factory']);
        $holder = BlogPost::factory()->inSeries($series, 1)->create(['slug' => 'import-test-holder']);
        $file = $this->writeFile($this->header(['slug' => 'import-test-clash', 'title' => 'Clash', 'series' => 'factory', 'part' => 1]) . $this->body);

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file])
            ->expectsOutputToContain("Part 1 of factory is already post #{$holder->id}")
            ->assertExitCode(1);

        $this->assertFalse(BlogPost::where('slug', 'import-test-clash')->exists());
        $this->assertSame(1, $holder->fresh()->series_part);
    }

    public function test_import_refuses_to_shrink_a_post_by_more_than_half_without_force(): void
    {
        BlogPost::factory()->create(['slug' => 'import-test-shrink', 'content' => $this->body . $this->body . $this->body]);
        $file = $this->writeFile($this->header(['slug' => 'import-test-shrink', 'title' => 'Shrink']) . $this->body);

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file])->assertExitCode(1);
        $this->assertStringStartsWith($this->body . $this->body, BlogPost::where('slug', 'import-test-shrink')->value('content'));

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file, '--force' => true])->assertExitCode(0);
        $this->assertSame($this->body, BlogPost::where('slug', 'import-test-shrink')->value('content'));
    }

    public function test_import_refuses_a_file_without_a_header(): void
    {
        $file = $this->writeFile($this->body);

        $this->artisan('blog:manage', ['action' => 'import', '--file' => $file])->assertExitCode(1);

        $this->assertSame(0, BlogPost::count());
    }
}
