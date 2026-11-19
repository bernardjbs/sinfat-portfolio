<?php

namespace Tests\Feature;

use App\Models\NotebookSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class NotebookToProdScriptTest extends TestCase
{
    use RefreshDatabase;

    // A committed snapshot: loaded by the migration, so the script's own artisan process can read it
    private const STAMP = '2026-11-09-2327';

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        // Stands in for the server: records the command it was given and the page piped to it
        $this->dir = sys_get_temp_dir() . '/notebook-to-prod-' . uniqid();
        mkdir($this->dir);
        file_put_contents("{$this->dir}/remote", "#!/bin/bash\nprintf '%s' \"\$1\" > {$this->dir}/command\ncat > {$this->dir}/page\n");
        chmod("{$this->dir}/remote", 0755);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob("{$this->dir}/*"));
        rmdir($this->dir);

        parent::tearDown();
    }

    private function send(string ...$args): Process
    {
        $process = new Process(['bash', base_path('scripts/notebook-to-prod.sh'), ...$args], base_path(), [
            'SINFAT_REMOTE' => "{$this->dir}/remote",
            'APP_ENV' => 'testing',
        ]);
        $process->run();

        return $process;
    }

    // scripts/notebook-to-prod.sh

    public function test_the_stored_page_is_piped_to_the_server_import(): void
    {
        $process = $this->send(self::STAMP);

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertSame(NotebookSnapshot::where('stamp', self::STAMP)->value('html'), file_get_contents("{$this->dir}/page"));
        $this->assertStringContainsString('notebook:manage import --prepared --stamp=' . self::STAMP . ' --file=-', file_get_contents("{$this->dir}/command"));
        $this->assertStringNotContainsString('--force', file_get_contents("{$this->dir}/command"));
    }

    public function test_force_is_passed_on_to_the_server(): void
    {
        $this->assertSame(0, $this->send(self::STAMP, '--force')->getExitCode());

        $this->assertStringEndsWith('--file=- --force', file_get_contents("{$this->dir}/command"));
    }

    public function test_any_other_argument_is_refused_before_reaching_the_server(): void
    {
        $this->assertNotSame(0, $this->send(self::STAMP, '--force; touch /tmp/injected')->getExitCode());

        $this->assertFileDoesNotExist("{$this->dir}/command");
    }
}
