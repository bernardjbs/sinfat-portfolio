<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Services\SeriesService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogController extends Controller
{
    public function __construct(private SeriesService $seriesService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->seriesService->loadSeriesFor(BlogPost::published());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $direction = $request->input('sort') === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('published_at', $direction);

        $posts = $query->paginate(10)->appends($request->only(['search', 'sort']));

        return BlogPostResource::collection($posts);
    }

    public function show(string $slug): BlogPostResource
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $this->seriesService->loadSeriesFor($post);

        // Series posts step through their series by part; standalone posts by date
        if ($post->series) {
            ['previous' => $previous, 'next' => $next] = $this->seriesService->neighbours($post);
        } else {
            $next = BlogPost::published()
                ->where('published_at', '>', $post->published_at)
                ->orderBy('published_at', 'asc')
                ->first(['title', 'slug']);

            $previous = BlogPost::published()
                ->where('published_at', '<', $post->published_at)
                ->orderBy('published_at', 'desc')
                ->first(['title', 'slug']);
        }

        return (new BlogPostResource($post))
            ->additional([
                'next' => $this->link($next),
                'previous' => $this->link($previous),
                'series_parts' => $post->series
                    ? $post->series->publishedPosts()->get(['title', 'slug', 'series_part'])
                        ->map(fn (BlogPost $part) => $this->link($part))
                    : null,
            ]);
    }

    private function link(?BlogPost $post): ?array
    {
        if (! $post) {
            return null;
        }

        $link = ['title' => $post->title, 'slug' => $post->slug];

        if ($post->series_part !== null) {
            $link['part'] = $post->series_part;
        }

        return $link;
    }
}
