# project-blog-writer

Drafts a sinfat.com blog post and saves it as a draft in its series. Never publishes.

**Use:** `Write a blog post from <summary file | post brief>`, or `Write a blog post:` followed by a
title and notes.

**Produces:** a draft via `php artisan blog:manage create`, in the named series, `ai_generated` true.
Bernard reviews and publishes in `/admin/blog`.

**Reads on demand:** `references/managing-posts.md` (commands and flags),
`references/summary-mapping.md` (HISTORICAL plan for the finished portfolio series).
