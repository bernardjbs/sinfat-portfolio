<?php

namespace App\Agents;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\Ollama\Ollama;

class GuestBlogWriterAgent extends Agent
{
    public function __construct(private string $apiKey)
    {
        parent::__construct();
    }

    protected function provider(): AIProviderInterface
    {
        $provider = config('services.ai.provider');

        return match ($provider) {
            'ollama' => new Ollama(
                url: config('services.ollama.url'),
                model: config('services.ollama.model'),
            ),
            'gemini' => new Gemini(
                key: $this->apiKey,
                model: config('services.gemini.model'),
            ),
            default => new Anthropic(
                key: $this->apiKey,
                model: config('services.anthropic.model'),
            ),
        };
    }

    protected function instructions(): string
    {
        return <<<'PROMPT'
        You are a creative writing assistant. Help the user write clear, engaging content on any topic they choose.
        Use markdown formatting with appropriate headings, code blocks, and bullet points.
        Length: 400–800 words unless instructed otherwise.
        PROMPT;
    }
}
