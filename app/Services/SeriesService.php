<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SeriesService
{
    /**
     * Series with at least one published part: being written first, then complete,
     * each group by latest published part, newest first.
     */
    public function listPublic(): Collection
    {
        return $this->withPublishedStats(Series::query())
            ->get()
            ->filter(fn (Series $series) => $series->part_count > 0)
            ->sort(fn (Series $a, Series $b) => [$this->statusRank($a), $b->last_published_at, $a->slug]
                <=> [$this->statusRank($b), $a->last_published_at, $b->slug])
            ->values();
    }

    /**
     * One public series with its published parts in order, or null when it has none.
     */
    public function findPublic(string $slug): ?Series
    {
        $series = $this->withPublishedStats(Series::query())
            ->where('slug', $slug)
            ->with('publishedPosts')
            ->first();

        return $series && $series->part_count > 0 ? $series : null;
    }

    /**
     * Load the series of each post with its published part count, for the "Part N of M" label.
     */
    public function loadSeriesFor(Builder|BlogPost $target): Builder|BlogPost
    {
        $relation = ['series' => fn ($q) => $q->withCount(['posts as part_count' => fn ($p) => $p->published()])];

        return $target instanceof BlogPost ? $target->load($relation) : $target->with($relation);
    }

    /**
     * Previous and next published parts in the post's series, by part number.
     *
     * @return array{previous: ?BlogPost, next: ?BlogPost}
     */
    public function neighbours(BlogPost $post): array
    {
        $parts = BlogPost::published()->where('series_id', $post->series_id);

        return [
            'previous' => (clone $parts)->where('series_part', '<', $post->series_part)
                ->orderByDesc('series_part')->first(['title', 'slug', 'series_part']),
            'next' => (clone $parts)->where('series_part', '>', $post->series_part)
                ->orderBy('series_part')->first(['title', 'slug', 'series_part']),
        ];
    }

    private function withPublishedStats(Builder $query): Builder
    {
        return $query
            ->withCount(['posts as part_count' => fn ($q) => $q->published()])
            ->withMax(['posts as last_published_at' => fn ($q) => $q->published()], 'published_at');
    }

    private function statusRank(Series $series): int
    {
        return $series->status === 'writing' ? 0 : 1;
    }
}
