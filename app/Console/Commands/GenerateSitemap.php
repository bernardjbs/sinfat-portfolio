<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap.xml file';

    public function handle(): int
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'))
            ->add(Url::create('/about')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/projects')->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create('/blog')->setPriority(0.9)->setChangeFrequency('daily'))
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

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated at public/sitemap.xml');

        return self::SUCCESS;
    }
}
