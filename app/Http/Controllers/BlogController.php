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
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(10);

        return BlogPostResource::collection($posts);
    }

    public function show(string $slug): BlogPostResource
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new BlogPostResource($post);
    }
}
