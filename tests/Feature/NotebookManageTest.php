<?php

namespace Tests\Feature;

use App\Models\NotebookSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class NotebookManageTest extends TestCase
{
    use RefreshDatabase;

    private const STAMP = '2026-12-01-0900';

    /** @var list<string> */
    private array $cleanup = [];

    protected function tearDown(): void
    {
        foreach ($this->cleanup as $path) {
            @unlink($path);
            @rmdir(dirname($path));
        }

        parent::tearDown();
    }

    private function source(string $body, string $stamp = self::STAMP): string
    {
        $dir = sys_get_temp_dir() . '/notebook-test-' . uniqid();
        mkdir($dir);
        $path = "{$dir}/{$stamp}.html";
        file_put_contents($path, "<title>My Software Factory, {$stamp}</title>\n{$body}");

        return $this->cleanup[] = $path;
    }

    private function import(array $options): \Illuminate\Testing\PendingCommand
    {
        return $this->artisan('notebook:manage', ['action' => 'import'] + $options);
    }

    // notebook:manage import (from a factory original)

    public function test_import_points_links_to_published_snapshots_at_their_page(): void
    {
        $file = $this->source('<a class="snap" href="2026-11-09-2327.html">Snapshot</a> <a class="snap" href="' . self::STAMP . '.html">Snapshot</a>');

        $this->import(['--file' => $file])->assertExitCode(0);

        $this->assertStringContainsString(
            '<a class="snap" href="/notebook/2026-11-09-2327/">Snapshot</a> <a class="snap" href="/notebook/' . self::STAMP . '/">Snapshot</a>',
            NotebookSnapshot::where('stamp', self::STAMP)->value('html')
        );
    }

    public function test_import_drops_links_to_unpublished_snapshots_with_their_text(): void
    {
        $file = $this->source('<span>Entry. <a class="snap" href="2026-11-10-2015.html">Snapshot</a></span>');

        $this->import(['--file' => $file])->assertExitCode(0);

        $this->assertStringContainsString('<span>Entry. </span>', NotebookSnapshot::where('stamp', self::STAMP)->value('html'));
    }

    public function test_import_unwraps_local_file_links_to_their_text(): void
    {
        $file = $this->source('<a href="file:///Users/someone/docs/backlog.md">Backlog</a> · <a href="https://example.com/">Video</a>');

        $this->import(['--file' => $file])->assertExitCode(0);

        $this->assertStringContainsString('Backlog · <a href="https://example.com/">Video</a>', NotebookSnapshot::where('stamp', self::STAMP)->value('html'));
    }

    public function test_import_changes_nothing_but_links(): void
    {
        $body = "<style>.snap{color:red}</style>\n<p>Plain text &amp; <code>code</code>.</p>\n";
        $file = $this->source($body);

        $this->import(['--file' => $file])->assertExitCode(0);

        $this->assertSame(file_get_contents($file), NotebookSnapshot::where('stamp', self::STAMP)->value('html'));
    }

    public function test_import_refuses_a_malformed_stamp_and_stdin_without_one(): void
    {
        $file = $this->source('<p>Body.</p>');

        $this->import(['--file' => $file, '--stamp' => 'not-a-stamp'])->assertExitCode(1);
        $this->import(['--file' => '-'])->assertExitCode(1);

        $this->assertDatabaseMissing('notebook_snapshots', ['stamp' => self::STAMP]);
    }

    public function test_import_refuses_a_page_whose_title_names_another_stamp(): void
    {
        $file = $this->source('<p>Body.</p>');

        $this->import(['--file' => $file, '--stamp' => '2026-12-02-0900'])->assertExitCode(1);

        $this->assertDatabaseMissing('notebook_snapshots', ['stamp' => '2026-12-02-0900']);
    }

    public function test_import_refuses_scripts_and_local_paths(): void
    {
        foreach (['<script>alert(1)</script>', '<SCRIPT src="x.js"></SCRIPT>', '<p>See /Users/someone/notes.md</p>'] as $body) {
            $this->import(['--file' => $this->source($body)])->assertExitCode(1);
        }

        $this->assertDatabaseMissing('notebook_snapshots', ['stamp' => self::STAMP]);
    }

    public function test_import_refuses_to_replace_a_snapshot_without_force(): void
    {
        $this->import(['--file' => $this->source('<p>First.</p>')])->assertExitCode(0);
        $this->import(['--file' => $this->source('<p>Second.</p>')])->assertExitCode(1);

        $this->assertStringContainsString('First.', NotebookSnapshot::where('stamp', self::STAMP)->value('html'));

        $this->import(['--file' => $this->source('<p>Second.</p>'), '--force' => true])->assertExitCode(0);

        $this->assertStringContainsString('Second.', NotebookSnapshot::where('stamp', self::STAMP)->value('html'));
    }

    // notebook:manage import --prepared (production, receiving a local copy)

    public function test_prepared_import_stores_the_bytes_unchanged(): void
    {
        $html = "<title>My Software Factory, " . self::STAMP . "</title>\n<a href=\"/notebook/2026-11-09-2327/\">Snapshot</a>";
        $file = $this->source('');
        file_put_contents($file, $html);

        $this->import(['--file' => $file, '--prepared' => true])->assertExitCode(0);

        $this->assertSame($html, NotebookSnapshot::where('stamp', self::STAMP)->value('html'));
    }

    public function test_prepared_import_refuses_a_link_to_a_snapshot_missing_here(): void
    {
        $file = $this->source('');
        file_put_contents($file, "<title>My Software Factory, " . self::STAMP . "</title>\n<a href=\"/notebook/2026-11-30-0800/\">Snapshot</a>");

        $this->import(['--file' => $file, '--prepared' => true])
            ->expectsOutputToContain('2026-11-30-0800')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('notebook_snapshots', ['stamp' => self::STAMP]);
    }

    public function test_prepared_import_refuses_an_original_that_was_never_prepared(): void
    {
        $file = $this->source('<a class="snap" href="2026-11-09-2327.html">Snapshot</a>');

        $this->import(['--file' => $file, '--prepared' => true])->assertExitCode(1);

        $this->assertDatabaseMissing('notebook_snapshots', ['stamp' => self::STAMP]);
    }

    // notebook:manage export

    public function test_export_writes_the_stored_page_and_nothing_else(): void
    {
        $this->assertSame(0, Artisan::call('notebook:manage', ['action' => 'export', '--stamp' => '2026-11-09-2327']));
        $this->assertSame(file_get_contents(resource_path('notebook/2026-11-09-2327.html')), Artisan::output());

        $this->artisan('notebook:manage', ['action' => 'export', '--stamp' => '2026-01-01-0000'])->assertExitCode(1);
    }
}
