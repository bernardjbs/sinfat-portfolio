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
        $data = $request->validated();

        if (($data['status'] ?? null) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);

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
        $data = $request->validated();

        if (($data['status'] ?? null) === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

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
