<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class BlogPageController extends Controller
{
    /**
     * Serve the SPA shell for /blog/{slug} with the post's own meta, so shared links
     * preview the post and a missing or draft slug answers 404.
     */
    public function __invoke(string $slug): Response
    {
        $post = BlogPost::published()->where('slug', $slug)->first();

        if (! $post) {
            return response()->view('app', [], 404);
        }

        return response()->view('app', [
            'meta' => [
                'title'       => $post->title,
                'description' => $post->excerpt,
                'url'         => rtrim(config('app.url'), '/')."/blog/{$post->slug}",
                'type'        => 'article',
            ],
        ]);
    }
}
