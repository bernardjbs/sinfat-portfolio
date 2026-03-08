<?php

namespace Tests\Feature;

use App\Models\AiSession;
use App\Models\User;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        $admin = User::factory()->create();
        return $this->actingAs($admin, 'sanctum');
    }

    // POST /api/admin/ai/generate — auth guard

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->postJson('/api/admin/ai/generate', [
            'topic' => 'Laravel SSE streaming',
        ])->assertUnauthorized();
    }

    // POST /api/admin/ai/generate — validation

    public function test_topic_is_required(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/ai/generate', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['topic']);
    }

    public function test_topic_must_be_string(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/ai/generate', ['topic' => 12345])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['topic']);
    }

    public function test_topic_max_length_is_500(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/ai/generate', ['topic' => str_repeat('a', 501)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['topic']);
    }

    public function test_context_max_length_is_2000(): void
    {
        $this->actingAsAdmin()
            ->postJson('/api/admin/ai/generate', [
                'topic' => 'Test topic',
                'context' => str_repeat('a', 2001),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['context']);
    }

    public function test_context_is_optional(): void
    {
        $this->mock(AiService::class, function ($mock) {
            $mock->shouldReceive('streamBlogGeneration')
                ->once();
        });

        $response = $this->actingAsAdmin()
            ->postJson('/api/admin/ai/generate', [
                'topic' => 'Laravel SSE streaming',
            ]);

        $response->assertOk();
        // Stream the response to trigger the callback
        $response->streamedContent();
    }

    // POST /api/admin/ai/generate — response headers

    public function test_response_has_sse_headers(): void
    {
        $this->mock(AiService::class, function ($mock) {
            $mock->shouldReceive('streamBlogGeneration')
                ->once();
        });

        $response = $this->actingAsAdmin()
            ->postJson('/api/admin/ai/generate', [
                'topic' => 'Laravel SSE streaming',
            ]);

        $response->assertOk();
        $response->streamedContent();

        $this->assertStringContainsString('text/event-stream', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $response->assertHeader('X-Accel-Buffering', 'no');
    }

    // POST /api/admin/ai/generate — ai_sessions logging

    public function test_generate_creates_ai_session_record(): void
    {
        $this->mock(AiService::class, function ($mock) {
            $mock->shouldReceive('streamBlogGeneration')
                ->once()
                ->andReturnUsing(function ($topic, $context, $identifier, $type) {
                    AiSession::create([
                        'identifier' => $identifier,
                        'type'       => $type,
                        'topic'      => $topic,
                        'model'      => config('services.anthropic.model'),
                        'status'     => 'completed',
                    ]);
                });
        });

        $admin = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/ai/generate', [
                'topic' => 'Test AI Generation',
            ]);

        $response->assertOk();
        $response->streamedContent();

        $this->assertDatabaseHas('ai_sessions', [
            'identifier' => $admin->email,
            'type'       => 'admin',
            'topic'      => 'Test AI Generation',
            'status'     => 'completed',
        ]);
    }
}
