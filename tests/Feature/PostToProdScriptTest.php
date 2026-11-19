<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Symfony\Component\Process\Process;
use Tests\TestCase;

// No RefreshDatabase: the script exports from its own artisan process, which cannot see a test transaction
class PostToProdScriptTest extends TestCase
{
    private string $dir;

    private BlogPost $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post = BlogPost::factory()->draft()->create(['slug' => 'post-to-prod-script-test']);

        // Stands in for the server: records the command it was given and the file piped to it
        $this->dir = sys_get_temp_dir() . '/post-to-prod-' . uniqid();
        mkdir($this->dir);
        file_put_contents("{$this->dir}/remote", "#!/bin/bash\nprintf '%s' \"\$1\" > {$this->dir}/command\ncat > {$this->dir}/file\n");
        chmod("{$this->dir}/remote", 0755);
    }

    protected function tearDown(): void
    {
        $this->post->delete();
        @unlink(storage_path('app/blog-exports/post-to-prod-script-test.md'));
        array_map('unlink', glob("{$this->dir}/*"));
        rmdir($this->dir);

        parent::tearDown();
    }

    private function send(string ...$args): Process
    {
        $process = new Process(['bash', base_path('scripts/post-to-prod.sh'), (string) $this->post->id, ...$args], base_path(), [
            'SINFAT_REMOTE' => "{$this->dir}/remote",
            'APP_ENV' => 'testing',
        ]);
        $process->run();

        return $process;
    }

    // scripts/post-to-prod.sh

    public function test_publish_is_passed_on_to_the_server_import(): void
    {
        $process = $this->send('--publish');

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertStringEndsWith('blog:manage import --file=- --publish', file_get_contents("{$this->dir}/command"));
        $this->assertStringContainsString('slug: "post-to-prod-script-test"', file_get_contents("{$this->dir}/file"));
    }

    public function test_draft_sends_without_publishing(): void
    {
        $this->assertSame(0, $this->send('--draft')->getExitCode());

        $this->assertStringEndsWith('blog:manage import --file=-', file_get_contents("{$this->dir}/command"));
    }

    public function test_force_is_passed_on_with_the_choice(): void
    {
        $this->assertSame(0, $this->send('--draft', '--force')->getExitCode());

        $this->assertStringEndsWith('--file=- --force', file_get_contents("{$this->dir}/command"));
    }

    public function test_a_send_without_a_choice_is_refused_naming_both(): void
    {
        $process = $this->send();

        $this->assertNotSame(0, $process->getExitCode());
        $this->assertStringContainsString('--publish', $process->getErrorOutput());
        $this->assertStringContainsString('--draft', $process->getErrorOutput());
        $this->assertFileDoesNotExist("{$this->dir}/command");
    }

    public function test_both_choices_at_once_are_refused(): void
    {
        $this->assertNotSame(0, $this->send('--publish', '--draft')->getExitCode());

        $this->assertFileDoesNotExist("{$this->dir}/command");
    }

    public function test_any_other_argument_is_refused_before_reaching_the_server(): void
    {
        $this->assertNotSame(0, $this->send('--publish', '--force; touch /tmp/injected')->getExitCode());

        $this->assertFileDoesNotExist("{$this->dir}/command");
    }
}
