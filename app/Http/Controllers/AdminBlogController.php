<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminBlogController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $posts = BlogPost::orderByDesc('created_at')->paginate(15);

        return BlogPostResource::collection($posts);
    }

    public function store(StoreBlogPostRequest $request): \Illuminate\Http\JsonResponse
    {
        $post = BlogPost::create($request->validated());

        return (new BlogPostResource($post))->response()->setStatusCode(201);
    }

    public function show(int $id): BlogPostResource
    {
        $post = BlogPost::findOrFail($id);

        return new BlogPostResource($post);
    }

    public function update(StoreBlogPostRequest $request, int $id): BlogPostResource
    {
        $post = BlogPost::findOrFail($id);
        $post->update($request->validated());

        return new BlogPostResource($post);
    }

    public function destroy(int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post deleted'], 200);
    }

    public function togglePublish(int $id): BlogPostResource
    {
        $post = BlogPost::findOrFail($id);

        if ($post->status === 'published') {
            $post->update(['status' => 'draft', 'published_at' => null]);
        } else {
            $post->update(['status' => 'published', 'published_at' => now()]);
        }

        return new BlogPostResource($post);
    }
}
