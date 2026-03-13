<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;

class BlogManage extends Command
{
    protected $signature = 'blog:manage
        {action : create|update|export}
        {--id= : Post ID (required for update/export)}
        {--file= : Markdown file path for content}
        {--title= : Post title (create only)}
        {--excerpt= : Post excerpt}
        {--category=development : Post category}
        {--ai-model=claude-sonnet-4-5 : AI model name}
        {--force : Skip confirmation prompt}';

    protected $description = 'Create, update, or export blog posts safely';

    public function handle(): int
    {
        return match ($this->argument('action')) {
            'create' => $this->createPost(),
            'update' => $this->updatePost(),
            'export' => $this->exportPost(),
            default => $this->abort('Unknown action. Use: create, update, or export.'),
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

        $content = file_get_contents($file);

        if (strlen($content) < 100) {
            $this->error("Content is only " . strlen($content) . " bytes. That's suspiciously short. Aborting.");
            return 1;
        }

        $post = BlogPost::create([
            'title'        => $title,
            'excerpt'      => $this->option('excerpt') ?? '',
            'content'      => $content,
            'category'     => $this->option('category'),
            'status'       => 'draft',
            'ai_generated' => true,
            'ai_model'     => $this->option('ai-model'),
        ]);

        $this->info("Created post #{$post->id}: {$post->title}");
        $this->line("  Slug: {$post->slug}");
        $this->line("  Length: " . strlen($content) . " bytes");
        $this->line("  Status: draft");

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

            $newContent = file_get_contents($file);
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

        if (empty($updates)) {
            $this->warn('Nothing to update. Use --file and/or --excerpt.');
            return 1;
        }

        // Confirm
        $this->info("Updating post #{$id}: {$post->title}");

        if (! $this->option('force') && ! $this->confirm('Proceed?')) {
            $this->line('Cancelled.');
            return 0;
        }

        $post->update($updates);
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
        file_put_contents($filename, $post->content);

        $this->info("Exported post #{$id} to {$filename}");
        $this->line("  Length: " . strlen($post->content) . " bytes");

        return 0;
    }

    private function abort(string $message): int
    {
        $this->error($message);
        return 1;
    }
}
