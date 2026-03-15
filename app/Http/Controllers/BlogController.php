<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = BlogPost::published();

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

        $next = BlogPost::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first(['title', 'slug']);

        $previous = BlogPost::published()
            ->where('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->first(['title', 'slug']);

        return (new BlogPostResource($post))
            ->additional([
                'next' => $next ? ['title' => $next->title, 'slug' => $next->slug] : null,
                'previous' => $previous ? ['title' => $previous->title, 'slug' => $previous->slug] : null,
            ]);
    }
}
