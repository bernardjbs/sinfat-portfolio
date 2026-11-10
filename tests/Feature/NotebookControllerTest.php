<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotebookControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function stamps(): array
    {
        return array_map(fn ($path) => basename($path, '.html'), glob(resource_path('notebook/*.html')));
    }

    // GET /notebook/{stamp}

    public function test_each_snapshot_is_served_as_its_own_page_not_the_spa(): void
    {
        $stamps = $this->stamps();
        $this->assertGreaterThanOrEqual(6, count($stamps));

        foreach ($stamps as $stamp) {
            $response = $this->get("/notebook/{$stamp}/")->assertOk();

            $this->assertStringStartsWith('text/html', $response->headers->get('Content-Type'));
            $html = $response->getFile()->getContent();
            $this->assertStringContainsString("<title>My Software Factory, {$stamp}</title>", $html);
            $this->assertStringNotContainsString('<div id="app">', $html);
        }
    }

    public function test_published_snapshots_carry_no_local_file_links(): void
    {
        foreach ($this->stamps() as $stamp) {
            $this->assertStringNotContainsString('file://', file_get_contents(resource_path("notebook/{$stamp}.html")), $stamp);
        }
    }

    public function test_links_between_snapshots_point_at_published_snapshots(): void
    {
        $targets = [];
        foreach ($this->stamps() as $stamp) {
            preg_match_all('/href="([^"#]*\d{4}-\d{2}-\d{2}-\d{4}[^"]*)"/', file_get_contents(resource_path("notebook/{$stamp}.html")), $m);
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

    public function test_malformed_stamp_returns_404_even_when_a_file_matches(): void
    {
        // A file whose name is not a stamp must still never be served
        $planted = resource_path('notebook/not-a-stamp.html');
        file_put_contents($planted, '<title>planted</title>');

        try {
            $this->get('/notebook/not-a-stamp/')->assertNotFound();
        } finally {
            unlink($planted);
        }

        $this->get('/notebook/2026-11-09-2327.html')->assertNotFound();
    }
}
