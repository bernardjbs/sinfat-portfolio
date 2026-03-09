## Module 10 — Sitemap + SEO
> 🟢 Sonnet

### Goal
Sitemap generated, meta tags per page, robots.txt in place.

### Tasks
- [x] Install `spatie/laravel-sitemap`: `composer require spatie/laravel-sitemap`
- [x] Create `GenerateSitemap` artisan command
- [x] Add meta tags per route (title, description, og:title, og:description)
- [x] Create `public/robots.txt`
- [x] Add sitemap generation to deploy pipeline (Module 1)
- [ ] Submit to Google Search Console after launch

### Technical Detail

**GenerateSitemap command:**
```php
Sitemap::create()
    ->add(Url::create('/')->setPriority(1.0))
    ->add(Url::create('/about')->setPriority(0.8))
    ->add(Url::create('/projects')->setPriority(0.8))
    ->add(Url::create('/blog')->setPriority(0.9))
    ->add(Url::create('/uses')->setPriority(0.6))
    ->add(Url::create('/contact')->setPriority(0.7))
    ->add(Url::create('/playground')->setPriority(0.7));

BlogPost::published()->each(fn($post) =>
    $sitemap->add(
        Url::create("/blog/{$post->slug}")
            ->setLastModificationDate($post->updated_at)
            ->setPriority(0.8)
    )
);

$sitemap->writeToFile(public_path('sitemap.xml'));
```

**robots.txt:**
```
User-agent: *
Allow: /
Disallow: /admin
Disallow: /login
Sitemap: https://sinfat.com/sitemap.xml
```

### Acceptance Criteria
- [x] `https://sinfat.com/sitemap.xml` accessible (generated on deploy)
- [x] All published blog posts included in sitemap
- [x] `robots.txt` blocks `/admin` and `/login`
- [x] Each page has correct `<title>` and `<meta description>`

### Dependencies
Module 5 (blog posts in sitemap)

---

