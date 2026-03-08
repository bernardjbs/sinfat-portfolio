<?php

namespace Tests\Feature;

use App\Models\AiSession;
use App\Models\GuestUsage;
use App\Services\GuestUsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Mockery;
use Tests\TestCase;

class PlaygroundControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('playground:127.0.0.1');

        // Mock the streaming service so tests don't hit the AI provider
        $mock = Mockery::mock(GuestUsageService::class);
        $mock->shouldReceive('streamGeneration')->andReturnNull();
        $this->app->instance(GuestUsageService::class, $mock);
    }

    // POST /api/playground/key

    public function test_set_key_requires_api_key(): void
    {
        $this->postJson('/api/playground/key', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('api_key');
    }

    public function test_set_key_rejects_invalid_prefix(): void
    {
        $this->postJson('/api/playground/key', ['api_key' => 'invalid-key-12345678901234567890'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('api_key');
    }

    public function test_set_key_rejects_short_key(): void
    {
        $this->postJson('/api/playground/key', ['api_key' => 'sk-ant-short'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('api_key');
    }

    public function test_set_key_accepts_valid_key(): void
    {
        $this->postJson('/api/playground/key', ['api_key' => 'sk-ant-valid-key-1234567890123456'])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_set_key_response_does_not_contain_key(): void
    {
        $response = $this->postJson('/api/playground/key', [
            'api_key' => 'sk-ant-valid-key-1234567890123456',
        ])->assertOk();

        $this->assertArrayNotHasKey('api_key', $response->json());
        $this->assertStringNotContainsString('sk-ant-', $response->getContent());
    }

    // POST /api/playground/generate

    public function test_generate_returns_stream_response(): void
    {
        $response = $this->postJson('/api/playground/generate', ['topic' => 'test topic']);

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/event-stream', $response->headers->get('Content-Type'));
        $response->assertHeader('X-Accel-Buffering', 'no');
    }

    public function test_generate_logs_to_guest_usage_table(): void
    {
        $this->postJson('/api/playground/generate', ['topic' => 'test logging']);

        $this->assertDatabaseHas('guest_usage', [
            'ip_address'   => '127.0.0.1',
            'topic'        => 'test logging',
            'used_own_key' => false,
        ]);
    }

    public function test_generate_logs_to_ai_sessions_table(): void
    {
        $this->postJson('/api/playground/generate', ['topic' => 'test logging']);

        $this->assertDatabaseHas('ai_sessions', [
            'identifier' => '127.0.0.1',
            'type'       => 'guest',
            'topic'      => 'test logging',
        ]);
    }

    public function test_generate_with_own_key_flags_used_own_key(): void
    {
        $this->withSession(['guest_api_key' => 'sk-ant-test-key-12345678901234567890'])
            ->postJson('/api/playground/generate', ['topic' => 'test own key']);

        $this->assertDatabaseHas('guest_usage', [
            'topic'        => 'test own key',
            'used_own_key' => true,
        ]);
    }

    public function test_guest_key_never_stored_in_database(): void
    {
        $this->postJson('/api/playground/key', [
            'api_key' => 'sk-ant-secret-key-12345678901234567890',
        ]);

        // setKey only stores in session — no DB records
        $this->assertDatabaseCount('guest_usage', 0);
        $this->assertDatabaseCount('ai_sessions', 0);
    }
}
