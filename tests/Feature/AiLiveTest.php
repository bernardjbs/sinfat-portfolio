<?php

namespace Tests\Feature;

use App\Agents\BlogWriterAgent;
use App\Agents\GuestBlogWriterAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NeuronAI\Chat\Messages\UserMessage;
use Tests\TestCase;

/**
 * Live AI integration tests — hit the real provider.
 *
 * These tests are excluded from the regular test suite.
 * Run manually with: php artisan test --filter AiLiveTest
 *
 * Requires AI_PROVIDER + matching API key in .env.
 */
class AiLiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $provider = config('services.ai.provider');
        $key = config("services.{$provider}.key");

        if (empty($key) || $provider === 'ollama') {
            $this->markTestSkipped("Live AI test requires a cloud provider with API key (current: {$provider})");
        }
    }

    public function test_blog_writer_agent_returns_text(): void
    {
        $agent = new BlogWriterAgent();

        $handler = $agent->chat(new UserMessage('Say hello in one sentence. Nothing else.'));

        $text = $handler->getMessage()->getContent();

        $this->assertNotEmpty($text, 'AI response should not be empty');
        $this->assertIsString($text);
        $this->assertGreaterThan(2, strlen($text), 'AI response should be more than 2 characters');
    }

    public function test_blog_writer_agent_streams_chunks(): void
    {
        $agent = new BlogWriterAgent();

        $handler = $agent->stream(new UserMessage('Say hello in one sentence. Nothing else.'));

        $chunks = [];
        foreach ($handler->events() as $chunk) {
            if ($chunk instanceof \NeuronAI\Chat\Messages\Stream\Chunks\TextChunk) {
                $chunks[] = $chunk->content;
            }
        }

        $this->assertNotEmpty($chunks, 'Stream should produce at least one text chunk');

        $fullText = implode('', $chunks);
        $this->assertGreaterThan(2, strlen($fullText), 'Streamed text should be more than 2 characters');
    }

    public function test_guest_agent_returns_text_with_provider_key(): void
    {
        $provider = config('services.ai.provider');
        $key = config("services.{$provider}.key");

        $agent = new GuestBlogWriterAgent($key);

        $handler = $agent->chat(new UserMessage('Say hello in one sentence. Nothing else.'));

        $text = $handler->getMessage()->getContent();

        $this->assertNotEmpty($text, 'Guest agent response should not be empty');
        $this->assertIsString($text);
    }
}
