## Module 6 — AI Integration (SSE + Neuron AI)
> 🔴 Opus — agent configuration and streaming implementation

### Goal
Admin can trigger an AI agent to draft a blog post. Draft streams token by token into the editor. Admin can chat with the model to refine before saving.

### Tasks
- [ ] Install Neuron AI: `composer require inspector-apm/neuron-ai`
- [ ] Configure Anthropic API key in `.env`
- [ ] Build `AiController` with SSE streaming endpoint
- [ ] Build `BlogWriterAgent` using Neuron AI
- [ ] Build streaming chat interface in `AdminBlogEditor.vue`
- [ ] Log AI sessions to `ai_sessions` table

### Technical Detail

**`.env`:**
```
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_MODEL=claude-sonnet-4-5
```

**BlogWriterAgent:**
```php
use NeuronAI\Agent;
use NeuronAI\Providers\Anthropic;

class BlogWriterAgent extends Agent {
    protected function provider(): Anthropic {
        return new Anthropic(
            apiKey: config('services.anthropic.key'),
            model: config('services.anthropic.model'),
        );
    }

    protected function instructions(): string {
        return "You are an expert technical blog writer specialising in Laravel, 
                Vue, and AI development. Write clear, practical, developer-focused 
                content. Use markdown formatting. Be opinionated and specific.";
    }
}
```

**AiController — SSE streaming:**
```php
public function generate(Request $request) {
    $topic = $request->validated()['topic'];

    return response()->stream(function () use ($topic) {
        $agent = new BlogWriterAgent();

        $agent->stream("Write a blog post about: {$topic}")
            ->each(function ($chunk) {
                echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                ob_flush();
                flush();
            });

        echo "data: [DONE]\n\n";
        ob_flush();
        flush();
    }, 200, [
        'Content-Type'  => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no', // Nginx: disable buffering
    ]);
}
```

**Vue streaming — composable:**
```javascript
// useStream.js
export function useStream(url) {
    const content = ref('')
    const streaming = ref(false)

    function start(payload) {
        streaming.value = true
        content.value = ''

        const source = new EventSource(url)
        source.onmessage = (event) => {
            if (event.data === '[DONE]') {
                streaming.value = false
                source.close()
                return
            }
            content.value += JSON.parse(event.data).text
        }
        source.onerror = () => {
            streaming.value = false
            source.close()
        }
    }

    return { content, streaming, start }
}
```

### Acceptance Criteria
- [ ] Admin clicks "Generate" → content streams into editor token by token
- [ ] `[DONE]` signal stops the stream cleanly
- [ ] Streamed content lands in md-editor-v3 and is editable immediately
- [ ] AI session logged to `ai_sessions` table
- [ ] Nginx buffering disabled (`X-Accel-Buffering: no`)

### Dependencies
Modules 3, 4, 5

---

