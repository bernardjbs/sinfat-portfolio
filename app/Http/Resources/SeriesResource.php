<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'         => $this->slug,
            'title'        => $this->title,
            'pitch'        => $this->pitch,
            'status'       => $this->status,
            'part_count'   => $this->part_count,
            'last_updated' => $this->last_published_at
                ? Carbon::parse($this->last_published_at)->toISOString()
                : null,
            'parts'        => $this->whenLoaded('publishedPosts', fn () => $this->publishedPosts->map(fn ($post) => [
                'part'         => $post->series_part,
                'title'        => $post->title,
                'slug'         => $post->slug,
                'excerpt'      => $post->excerpt,
                'published_at' => $post->published_at?->toISOString(),
            ])),
        ];
    }
}
