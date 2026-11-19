<?php

namespace Tests\Feature;

use App\Models\NotebookSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotebookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function recordStamps(): array
    {
        return array_map(fn ($path) => basename($path, '.html'), glob(resource_path('notebook/*.html')));
    }

    // The seven copies committed before snapshots moved to the database

    public function test_the_committed_copies_are_loaded_byte_for_byte(): void
    {
        $stamps = $this->recordStamps();
        $this->assertGreaterThanOrEqual(7, count($stamps));

        foreach ($stamps as $stamp) {
            $this->assertSame(
                file_get_contents(resource_path("notebook/{$stamp}.html")),
                NotebookSnapshot::where('stamp', $stamp)->value('html'),
                $stamp
            );
        }
    }

    // GET /notebook/{stamp}

    public function test_each_snapshot_is_served_as_its_own_page_not_the_spa(): void
    {
        foreach (NotebookSnapshot::pluck('stamp') as $stamp) {
            $response = $this->get("/notebook/{$stamp}/")->assertOk();

            $this->assertStringStartsWith('text/html', $response->headers->get('Content-Type'));
            $response->assertSee("<title>My Software Factory, {$stamp}</title>", false);
            $response->assertDontSee('<div id="app">', false);
        }
    }

    public function test_a_snapshot_page_forbids_scripts(): void
    {
        $response = $this->get('/notebook/2026-11-09-2327/')->assertOk();

        $this->assertStringContainsString("script-src 'none'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_a_snapshot_page_loads_nothing_and_sends_nothing_elsewhere(): void
    {
        $policy = $this->get('/notebook/2026-11-09-2327/')->assertOk()->headers->get('Content-Security-Policy');

        foreach (["default-src 'none'", "form-action 'none'", "frame-ancestors 'none'", "base-uri 'none'"] as $rule) {
            $this->assertStringContainsString($rule, $policy);
        }
    }

    public function test_published_snapshots_carry_no_local_file_links(): void
    {
        foreach (NotebookSnapshot::all() as $snapshot) {
            $this->assertStringNotContainsString('file://', $snapshot->html, $snapshot->stamp);
        }
    }

    public function test_links_between_snapshots_point_at_published_snapshots(): void
    {
        $targets = [];
        foreach (NotebookSnapshot::pluck('html') as $html) {
            preg_match_all('/href="([^"#]*\d{4}-\d{2}-\d{2}-\d{4}[^"]*)"/', $html, $m);
            $targets = array_merge($targets, $m[1]);
        }

        $this->assertNotEmpty($targets);
        foreach (array_unique($targets) as $href) {
            $this->assertMatchesRegularExpression('#^/notebook/\d{4}-\d{2}-\d{2}-\d{4}/$#', $href);
            $this->get($href)->assertOk();
        }
    }

    public function test_unknown_snapshot_returns_404_with_the_app_shell(): void
    {
        $this->get('/notebook/2026-01-01-0000/')
            ->assertNotFound()
            ->assertSee('<div id="app">', false);
    }

    public function test_malformed_stamp_returns_404_even_when_a_row_matches(): void
    {
        // A row whose stamp is not a stamp must still never be served
        NotebookSnapshot::create(['stamp' => 'not-a-stamp', 'html' => '<title>planted</title>']);

        $this->get('/notebook/not-a-stamp/')->assertNotFound();
        $this->get('/notebook/2026-11-09-2327.html')->assertNotFound();
    }
}
