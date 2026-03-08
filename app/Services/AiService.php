<?php

namespace App\Services;

use App\Agents\BlogWriterAgent;
use App\Models\AiSession;
use NeuronAI\Chat\Messages\Stream\Adapters\VercelAIAdapter;
use NeuronAI\Chat\Messages\UserMessage;

class AiService
{
    /**
     * Stream a blog post generation via SSE.
     *
     * Yields SSE-formatted strings for use in a StreamedResponse.
     */
    public function streamBlogGeneration(string $topic, ?string $context, string $identifier, string $type = 'admin'): void
    {
        $prompt = "Write a blog post about: {$topic}";

        if (!empty($context)) {
            $prompt .= "\n\nAdditional context: {$context}";
        }

        $session = AiSession::create([
            'identifier' => $identifier,
            'type'       => $type,
            'topic'      => $topic,
            'model'      => config('services.' . config('services.ai.provider') . '.model'),
            'status'     => 'streaming',
        ]);

        try {
            $agent = new BlogWriterAgent();

            $handler = $agent->stream(new UserMessage($prompt));

            foreach ($handler->events() as $chunk) {
                if ($chunk instanceof \NeuronAI\Chat\Messages\Stream\Chunks\TextChunk) {
                    echo "data: " . json_encode(['text' => $chunk->content]) . "\n\n";
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                }
            }

            echo "data: [DONE]\n\n";
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            $session->update(['status' => 'completed']);

        } catch (\Exception $e) {
            echo "data: " . json_encode(['error' => 'Generation failed']) . "\n\n";
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            $session->update(['status' => 'failed']);
        }
    }
}
