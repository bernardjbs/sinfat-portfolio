<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BlogManage extends Command
{
    protected $signature = 'blog:manage
        {action : create|update|export|import|series}
        {subaction? : For series: create|update|complete}
        {--id= : Post ID (required for update/export)}
        {--file= : Markdown file path for content (import: - reads stdin)}
        {--title= : Post or series title (post create, series create/update)}
        {--excerpt= : Post excerpt}
        {--category=development : Post category}
        {--ai-model=claude-sonnet-4-5 : AI model name}
        {--series= : Series slug to put the post in (post create/update)}
        {--part= : Part number in the series; omit to append}
        {--slug= : Series slug (series actions)}
        {--pitch= : Series pitch (series create/update)}
        {--force : Skip confirmation prompt}';

    protected $description = 'Create, update, export or import blog posts and manage series safely';

    public function handle(): int
    {
        return match ($this->argument('action')) {
            'create' => $this->createPost(),
            'update' => $this->updatePost(),
            'export' => $this->exportPost(),
            'import' => $this->importPost(),
            'series' => $this->manageSeries(),
            default => $this->abort('Unknown action. Use: create, update, export, import, or series.'),
        };
    }

    private function createPost(): int
    {
        $title = $this->option('title');
        $file = $this->option('file');

        if (! $title) {
            $this->error('--title is required for create.');
            return 1;
        }

        if (! $file || ! file_exists($file)) {
            $this->error('--file is required and must exist.');
            return 1;
        }

        [, $content] = $this->splitHeader(file_get_contents($file));

        if (strlen($content) < 100) {
            $this->error("Content is only " . strlen($content) . " bytes. That's suspiciously short. Aborting.");
            return 1;
        }

        $placement = $this->seriesPlacement(null);

        if ($placement === false) {
            return 1;
        }

        $post = DB::transaction(function () use ($title, $content, $placement) {
            $post = BlogPost::create([
                'title'        => $title,
                'excerpt'      => $this->option('excerpt') ?? '',
                'content'      => $content,
                'category'     => $this->option('category'),
                'status'       => 'draft',
                'ai_generated' => true,
                'ai_model'     => $this->option('ai-model'),
            ]);

            if ($placement) {
                $this->placeInSeries($post, ...$placement);
            }

            return $post;
        });

        $this->info("Created post #{$post->id}: {$post->title}");
        $this->line("  Slug: {$post->slug}");
        $this->line("  Length: " . strlen($content) . " bytes");
        $this->line("  Status: draft");

        if ($placement) {
            $this->line("  Series: {$placement[0]->slug}, part {$post->series_part}");
        }

        return 0;
    }

    private function updatePost(): int
    {
        $id = $this->option('id');

        if (! $id) {
            $this->error('--id is required for update.');
            return 1;
        }

        $post = BlogPost::find($id);

        if (! $post) {
            $this->error("Post #{$id} not found.");
            return 1;
        }

        $updates = [];

        // Content from file
        if ($file = $this->option('file')) {
            if (! file_exists($file)) {
                $this->error("File not found: {$file}");
                return 1;
            }

            [, $newContent] = $this->splitHeader(file_get_contents($file));
            $oldLength = strlen($post->content);
            $newLength = strlen($newContent);

            if ($newLength < 100) {
                $this->error("New content is only {$newLength} bytes. That's suspiciously short. Aborting.");
                return 1;
            }

            if ($newLength < $oldLength * 0.5) {
                $this->error("New content ({$newLength} bytes) is less than half the original ({$oldLength} bytes). Aborting.");
                $this->line("If this is intentional, export the post first as a backup, then retry.");
                return 1;
            }

            $updates['content'] = $newContent;
            $this->line("  Content: {$oldLength} → {$newLength} bytes");
        }

        // Excerpt
        if ($excerpt = $this->option('excerpt')) {
            $updates['excerpt'] = $excerpt;
            $this->line("  Excerpt: updated");
        }

        $placement = $this->seriesPlacement($post);

        if ($placement === false) {
            return 1;
        }

        if ($placement) {
            $this->line("  Series: {$placement[0]->slug}, part " . ($placement[1] ?? 'appended'));
        }

        if (empty($updates) && ! $placement) {
            $this->warn('Nothing to update. Use --file, --excerpt, --series and/or --part.');
            return 1;
        }

        // Confirm
        $this->info("Updating post #{$id}: {$post->title}");

        if (! $this->option('force') && ! $this->confirm('Proceed?')) {
            $this->line('Cancelled.');
            return 0;
        }

        DB::transaction(function () use ($post, $updates, $placement) {
            if ($updates) {
                $post->update($updates);
            }

            if ($placement) {
                $this->placeInSeries($post, ...$placement);
            }
        });

        $this->info('Updated.');

        return 0;
    }

    private function exportPost(): int
    {
        $id = $this->option('id');

        if (! $id) {
            $this->error('--id is required for export.');
            return 1;
        }

        $post = BlogPost::find($id);

        if (! $post) {
            $this->error("Post #{$id} not found.");
            return 1;
        }

        $dir = storage_path('app/blog-exports');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $dir . '/' . $post->slug . '.md';
        file_put_contents($filename, $this->exportContents($post));

        $this->info("Exported post #{$id} to {$filename}");
        $this->line("  Length: " . strlen($post->content) . " bytes");

        return 0;
    }

    /**
     * Create or update a post from an exported file, matched by slug. Never publishes.
     */
    private function importPost(): int
    {
        $file = $this->option('file');

        if ($file === '-') {
            $raw = stream_get_contents(STDIN);
        } elseif ($file && file_exists($file)) {
            $raw = file_get_contents($file);
        } else {
            $this->error('--file is required and must exist (or - for stdin).');
            return 1;
        }

        [$meta, $content] = $this->splitHeader((string) $raw);

        if (empty($meta['slug']) || empty($meta['title'])) {
            $this->error('The file needs a header with slug and title. Make it with: php artisan blog:manage export --id=N');
            return 1;
        }

        if (strlen($content) < 100) {
            $this->error("Content is only " . strlen($content) . " bytes. That's suspiciously short. Aborting.");
            return 1;
        }

        $series = null;
        $part = isset($meta['part']) ? (int) $meta['part'] : null;

        if (! empty($meta['series'])) {
            $series = Series::where('slug', $meta['series'])->first();

            if (! $series) {
                $this->error("Series not found here: {$meta['series']}. Create it first: php artisan blog:manage series create --slug={$meta['series']} --title='...'");
                return 1;
            }
        }

        $post = BlogPost::where('slug', $meta['slug'])->first();

        if ($series && $part !== null) {
            $holder = BlogPost::where('series_id', $series->id)->where('series_part', $part)->first();

            if ($holder && $holder->id !== $post?->id) {
                $this->error("Part {$part} of {$series->slug} is already post #{$holder->id} ({$holder->slug}). Aborting.");
                return 1;
            }
        }

        if ($post && strlen($content) < strlen($post->content) * 0.5 && ! $this->option('force')) {
            $this->error('New content is less than half the current length. Aborting; pass --force if that is intended.');
            return 1;
        }

        $fields = [
            'title'        => $meta['title'],
            'excerpt'      => $meta['excerpt'] ?? '',
            'content'      => $content,
            'category'     => $meta['category'] ?? 'development',
            'ai_generated' => (bool) ($meta['ai_generated'] ?? true),
            'ai_model'     => $meta['ai_model'] ?? null,
        ];

        if ($post) {
            $dir = storage_path('app/blog-exports/backups');

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $backup = $dir . '/' . $post->slug . '-' . now()->format('Ymd-His') . '.md';
            file_put_contents($backup, $this->exportContents($post));
        }

        $post = DB::transaction(function () use ($post, $meta, $fields, $series, $part) {
            if ($post) {
                $post->update($fields);
            } else {
                $post = BlogPost::create($fields + ['slug' => $meta['slug'], 'status' => 'draft']);
            }

            // A header without a series leaves the post's series as it is
            if ($series && ($post->series_id !== $series->id || ($part !== null && $post->series_part !== $part))) {
                $this->placeInSeries($post, $series, $part);
            }

            return $post;
        });

        $this->info(($backup ?? null) ? "Updated post #{$post->id}: {$post->title}" : "Created post #{$post->id}: {$post->title}");
        $this->line("  Slug: {$post->slug}");
        $this->line("  Status: {$post->status}");

        if (isset($backup)) {
            $this->line("  Backup: {$backup}");
        }

        if ($post->series_id) {
            $this->line("  Series: {$post->series->slug}, part {$post->series_part}");
        }

        $this->line("  Review and publish: /admin/blog/{$post->id}/edit");

        return 0;
    }

    /**
     * The export format: a header of key: JSON value lines between --- markers, then the content.
     */
    private function exportContents(BlogPost $post): string
    {
        $meta = [
            'slug'         => $post->slug,
            'title'        => $post->title,
            'excerpt'      => $post->excerpt,
            'category'     => $post->category,
            'ai_generated' => (bool) $post->ai_generated,
            'ai_model'     => $post->ai_model,
            'series'       => $post->series?->slug,
            'part'         => $post->series_part,
        ];

        $lines = array_map(
            fn ($key, $value) => $key . ': ' . json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            array_keys($meta),
            $meta,
        );

        return "---\n" . implode("\n", $lines) . "\n---\n" . $post->content;
    }

    /**
     * [header fields, content]. A file without a header is all content.
     */
    private function splitHeader(string $raw): array
    {
        if (! str_starts_with($raw, "---\n") || ($end = strpos($raw, "\n---\n", 3)) === false) {
            return [[], $raw];
        }

        $meta = [];

        foreach (explode("\n", substr($raw, 4, $end - 4)) as $line) {
            if (preg_match('/^([a-z_]+): (.*)$/', $line, $match)) {
                $meta[$match[1]] = json_decode($match[2], true);
            }
        }

        return [$meta, substr($raw, $end + 5)];
    }

    private function manageSeries(): int
    {
        $slug = $this->option('slug');

        if (! $slug) {
            $this->error('--slug is required for series actions.');
            return 1;
        }

        $subaction = $this->argument('subaction');

        if ($subaction === 'create') {
            if (Series::where('slug', $slug)->exists()) {
                $this->error("Series already exists: {$slug}");
                return 1;
            }

            if (! $this->option('title')) {
                $this->error('--title is required for series create.');
                return 1;
            }

            Series::create([
                'slug'   => $slug,
                'title'  => $this->option('title'),
                'pitch'  => $this->option('pitch'),
                'status' => 'writing',
            ]);

            $this->info("Created series {$slug} (being written)");
            return 0;
        }

        $series = Series::where('slug', $slug)->first();

        if (! $series) {
            $this->error("Series not found: {$slug}");
            return 1;
        }

        if ($subaction === 'complete') {
            $series->update(['status' => 'complete']);
            $this->info("Series {$slug} marked complete");
            return 0;
        }

        if ($subaction === 'update') {
            $updates = array_filter([
                'title' => $this->option('title'),
                'pitch' => $this->option('pitch'),
            ], fn ($value) => $value !== null);

            if (empty($updates)) {
                $this->warn('Nothing to update. Use --title and/or --pitch.');
                return 1;
            }

            $series->update($updates);
            $this->info("Updated series {$slug}");
            return 0;
        }

        return $this->abort('Unknown series action. Use: series create, series update, or series complete.');
    }

    /**
     * The series and part asked for by --series/--part: null when neither is given, false on error.
     */
    private function seriesPlacement(?BlogPost $post): array|false|null
    {
        $slug = $this->option('series');
        $part = $this->option('part');

        if ($slug === null && $part === null) {
            return null;
        }

        $series = $slug !== null ? Series::where('slug', $slug)->first() : $post?->series;

        if (! $series) {
            $this->error($slug !== null ? "Series not found: {$slug}" : '--part needs --series: the post is not in a series.');
            return false;
        }

        if ($part !== null && (! ctype_digit((string) $part) || (int) $part < 1)) {
            $this->error('--part must be a whole number from 1.');
            return false;
        }

        return [$series, $part !== null ? (int) $part : null];
    }

    /**
     * Put the post at the given part, or at the end. A taken part moves up by one, with every part after it.
     */
    private function placeInSeries(BlogPost $post, Series $series, ?int $part): void
    {
        // Free the post's own slot first, so a move within its series never collides with itself
        $post->update(['series_id' => null, 'series_part' => null]);

        $siblings = BlogPost::where('series_id', $series->id);

        if ($part === null) {
            $part = (int) (clone $siblings)->max('series_part') + 1;
        } elseif ((clone $siblings)->where('series_part', $part)->exists()) {
            // Highest first, so no two posts ever share a part mid-shift
            (clone $siblings)->where('series_part', '>=', $part)->orderByDesc('series_part')->get()
                ->each(fn (BlogPost $later) => $later->update(['series_part' => $later->series_part + 1]));
        }

        $post->update(['series_id' => $series->id, 'series_part' => $part]);
    }

    private function abort(string $message): int
    {
        $this->error($message);
        return 1;
    }
}
