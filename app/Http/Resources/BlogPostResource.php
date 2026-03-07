<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
            'content'      => $this->when(
                $request->routeIs('api.blog.show'),
                fn() => Str::markdown((string) $this->content)
            ),
            'raw_content'  => $this->when(
                $request->routeIs('api.admin.blog.show'),
                $this->content
            ),
            'status'       => $this->when(
                $request->routeIs('api.admin.blog.*'),
                $this->status
            ),
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
}
