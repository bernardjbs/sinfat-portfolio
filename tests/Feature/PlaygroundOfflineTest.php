<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class PlaygroundOfflineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('playground:127.0.0.1');
        config(['services.ai.playground_enabled' => false]);
    }

    public function test_the_playground_is_off_unless_switched_on(): void
    {
        putenv('PLAYGROUND_ENABLED');
        $config = require config_path('services.php');

        $this->assertFalse($config['ai']['playground_enabled']);
    }

    public function test_generate_is_refused_while_offline_and_uses_no_free_generation(): void
    {
        $this->postJson('/api/playground/generate', ['topic' => 'anything'])
            ->assertStatus(503)
            ->assertJsonPath('error', 'playground_offline');

        $this->assertDatabaseCount('guest_usage', 0);
        $this->assertSame(3, RateLimiter::remaining('playground:127.0.0.1', 3));
    }

    public function test_saving_a_key_is_refused_while_offline(): void
    {
        $this->postJson('/api/playground/key', ['api_key' => 'sk-ant-valid-key-1234567890123456'])
            ->assertStatus(503)
            ->assertJsonPath('error', 'playground_offline');
    }

    public function test_status_reports_the_playground_offline(): void
    {
        $this->getJson('/api/playground/status')->assertOk()->assertJsonPath('enabled', false);
    }

    public function test_the_page_shell_tells_the_frontend_it_is_offline(): void
    {
        $this->get('/blog')->assertSee('<meta name="playground" content="off">', false);
    }
}
