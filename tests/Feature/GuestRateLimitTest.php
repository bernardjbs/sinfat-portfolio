<?php

namespace Tests\Feature;

use App\Services\GuestUsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Mockery;
use Tests\TestCase;

class GuestRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('playground:127.0.0.1');

        // Mock the service so tests don't hit the AI provider
        $mock = Mockery::mock(GuestUsageService::class);
        $mock->shouldReceive('streamGeneration')->andReturnNull();
        $this->app->instance(GuestUsageService::class, $mock);
    }

    public function test_first_request_is_allowed(): void
    {
        $this->postJson('/api/playground/generate', ['topic' => 'test topic'])
            ->assertStatus(200);
    }

    public function test_remaining_header_decrements(): void
    {
        $response = $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        $response->assertHeader('X-RateLimit-Remaining', 2);

        $response = $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        $response->assertHeader('X-RateLimit-Remaining', 1);

        $response = $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        $response->assertHeader('X-RateLimit-Remaining', 0);
    }

    public function test_fourth_request_is_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        }

        $this->postJson('/api/playground/generate', ['topic' => 'test topic'])
            ->assertStatus(429)
            ->assertJsonPath('error', 'limit_reached');
    }

    public function test_rate_limit_returns_correct_message(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        }

        $this->postJson('/api/playground/generate', ['topic' => 'test topic'])
            ->assertStatus(429)
            ->assertJsonFragment([
                'message' => 'You have used all 3 free generations. Please provide your Anthropic API key to continue.',
            ]);
    }

    public function test_rate_limit_returns_remaining_zero(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        }

        $this->postJson('/api/playground/generate', ['topic' => 'test topic'])
            ->assertStatus(429)
            ->assertJsonPath('remaining', 0);
    }

    public function test_request_with_session_key_bypasses_rate_limit(): void
    {
        // Exhaust free limit
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test topic']);
        }

        // Set API key in session — should bypass rate limit
        $this->withSession(['guest_api_key' => 'sk-ant-test-key-12345678901234567890'])
            ->postJson('/api/playground/generate', ['topic' => 'test topic'])
            ->assertStatus(200);
    }

    public function test_generate_requires_topic(): void
    {
        $this->postJson('/api/playground/generate', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('topic');
    }

    public function test_generate_rejects_topic_over_500_chars(): void
    {
        $this->postJson('/api/playground/generate', ['topic' => str_repeat('a', 501)])
            ->assertStatus(422)
            ->assertJsonValidationErrors('topic');
    }
}
