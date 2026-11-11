<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'category'     => $this->category,
            'series'       => $this->when(
                $this->relationLoaded('series'),
                fn() => $this->series ? [
                    'slug'   => $this->series->slug,
                    'title'  => $this->series->title,
                    'status' => $this->series->status,
                    'part'   => $this->series_part,
                    'total'  => $this->series->part_count,
                ] : null
            ),
            'content'      => $this->when(
                $request->routeIs('api.blog.show'),
                fn() => $this->renderMarkdown((string) $this->content)
            ),
            'raw_content'  => $this->when(
                $request->routeIs('api.admin.blog.show'),
                $this->content
            ),
            'status'       => $this->when(
                $request->routeIs('api.admin.blog.*'),
                $this->status
            ),
            'reading_time' => (int) max(1, ceil(str_word_count(strip_tags((string) $this->content)) / 200)),
            'published_at' => $this->published_at?->toISOString(),
            'ai_generated' => $this->ai_generated,
            'ai_model'     => $this->when(
                $request->routeIs('api.admin.blog.*'),
                $this->ai_model
            ),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->when(
                $request->routeIs('api.admin.blog.*'),
                $this->updated_at?->toISOString()
            ),
        ];
    }

    private function renderMarkdown(string $content): string
    {
        $environment = new Environment([
            'heading_permalink' => [
                'html_class'      => 'heading-permalink',
                'id_prefix'       => '',
                'fragment_prefix' => '',
                'insert'          => 'before',
                'symbol'          => '',
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        // Notebook snapshots open in a new tab, so the reader keeps their place in the post
        $environment->addEventListener(DocumentParsedEvent::class, function (DocumentParsedEvent $event) {
            foreach ($event->getDocument()->iterator() as $node) {
                if ($node instanceof Link && str_starts_with($node->getUrl(), '/notebook/')) {
                    $node->data->set('attributes/target', '_blank');
                    $node->data->set('attributes/rel', 'noopener');
                }
            }
        });

        $converter = new MarkdownConverter($environment);

        return $converter->convert($content)->getContent();
    }
}
