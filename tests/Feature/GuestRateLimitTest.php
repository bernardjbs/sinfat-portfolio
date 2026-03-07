<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GuestRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_first_three_requests_are_allowed(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test'])
                ->assertStatus(501); // stub response — not rate limited
        }
    }

    public function test_fourth_request_is_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test']);
        }

        $this->postJson('/api/playground/generate', ['topic' => 'test'])
            ->assertStatus(429)
            ->assertJsonPath('error', 'limit_reached');
    }

    public function test_rate_limit_message_is_correct(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/playground/generate', ['topic' => 'test']);
        }

        $this->postJson('/api/playground/generate', ['topic' => 'test'])
            ->assertStatus(429)
            ->assertJsonPath('message', 'Free limit reached. Provide your Anthropic API key to continue.');
    }

    public function test_rate_limit_counter_increments_in_cache(): void
    {
        $this->postJson('/api/playground/generate', ['topic' => 'test']);

        $this->assertEquals(1, Cache::get('guest_usage:127.0.0.1'));
    }
}
