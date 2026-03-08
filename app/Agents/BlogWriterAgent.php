<?php

namespace App\Agents;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\Ollama\Ollama;

class BlogWriterAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        $provider = config('services.ai.provider');

        return match ($provider) {
            'ollama' => new Ollama(
                url: config('services.ollama.url'),
                model: config('services.ollama.model'),
            ),
            'gemini' => new Gemini(
                key: config('services.gemini.key'),
                model: config('services.gemini.model'),
            ),
            default => new Anthropic(
                key: config('services.anthropic.key'),
                model: config('services.anthropic.model'),
            ),
        };
    }

    protected function instructions(): string
    {
        return <<<'PROMPT'
        You are an expert technical blog writer specialising in Laravel, Vue 3, and AI development.
        Write clear, practical, developer-focused content with real code examples.
        Use markdown formatting with appropriate headings, code blocks, and bullet points.
        Be opinionated and specific — avoid generic advice.
        Target audience: experienced developers who want depth, not hand-holding.
        Length: 600–1200 words unless instructed otherwise.
        PROMPT;
    }
}
