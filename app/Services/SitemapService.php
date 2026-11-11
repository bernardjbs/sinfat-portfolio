<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Series;
use Carbon\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapService
{
    public function __construct(private SeriesService $seriesService) {}

    /**
     * The sitemap as the database stands now. Built per request and never stored, so no publish
     * path has to remember to rebuild it and no file can go stale or belong to the wrong user.
     */
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'))
            ->add(Url::create('/about')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/projects')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/blog')->setPriority(0.9)->setChangeFrequency('daily'))
            ->add(Url::create('/blog/series')->setPriority(0.8)->setChangeFrequency('weekly'))
            ->add(Url::create('/uses')->setPriority(0.6)->setChangeFrequency('monthly'))
            ->add(Url::create('/contact')->setPriority(0.7)->setChangeFrequency('monthly'))
            ->add(Url::create('/playground')->setPriority(0.7)->setChangeFrequency('monthly'));

        BlogPost::published()
            ->orderByDesc('published_at')
            ->each(function (BlogPost $post) use ($sitemap) {
                $sitemap->add(
                    Url::create("/blog/{$post->slug}")
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency('weekly')
                );
            });

        $this->seriesService->listPublic()->each(function (Series $series) use ($sitemap) {
            $sitemap->add(
                Url::create("/blog/series/{$series->slug}")
                    ->setLastModificationDate(Carbon::parse($series->last_published_at))
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
            );
        });

        return $sitemap;
    }
}
