<?php

namespace App\Agents;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\Anthropic\Anthropic;

class BlogWriterAgent extends Agent
{
    protected function provider(): Anthropic
    {
        return new Anthropic(
            key: config('services.anthropic.key'),
            model: config('services.anthropic.model'),
        );
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
