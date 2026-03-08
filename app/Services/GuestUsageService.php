<?php

namespace App\Services;

use App\Agents\GuestBlogWriterAgent;
use App\Models\AiSession;
use App\Models\GuestUsage;
use NeuronAI\Chat\Messages\UserMessage;

class GuestUsageService
{
    /**
     * Stream a guest blog generation via SSE.
     *
     * Records are created by the controller before the stream starts.
     * This method handles the streaming and updates the status on completion.
     */
    public function streamGeneration(string $topic, string $apiKey, GuestUsage $guestUsage, AiSession $aiSession): void
    {
        try {
            $agent = new GuestBlogWriterAgent($apiKey);

            $prompt = "Write a blog post about: {$topic}";

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

            $guestUsage->update(['status' => 'completed']);
            $aiSession->update(['status' => 'completed']);

        } catch (\Exception $e) {
            echo "data: " . json_encode(['error' => 'Generation failed']) . "\n\n";
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            $guestUsage->update(['status' => 'failed']);
            $aiSession->update(['status' => 'failed']);
        }
    }
}
