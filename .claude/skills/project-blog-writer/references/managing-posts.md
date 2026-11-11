# Managing posts with `blog:manage`

Every post write goes through `php artisan blog:manage`. The live option list is
`php artisan blog:manage --help`; read it if a flag below fails.

## Create a draft

Write the markdown to a file first (no `# Title` line: the title is a field), then:

```bash
php artisan blog:manage create \
    --title="Your Title Here" \
    --excerpt="One sentence summary for the blog listing and RSS feed." \
    --file=storage/app/blog-exports/draft.md \
    --series=<series-slug> \
    --ai-model=<model that drafted it>
```

- Creates a **draft** with `ai_generated` true. Slug is generated from the title.
- `--ai-model` defaults to an old model name (`blog:manage --help` shows it): always pass the one that drafted the post.
- `--series` appends the post as the next part; `--part=<n>` places it, moving later parts up.
- Rejects content under 100 bytes.

## Update a draft

Export first, as the backup:

```bash
php artisan blog:manage export --id=10
# edit storage/app/blog-exports/{slug}.md
php artisan blog:manage update --id=10 --file=storage/app/blog-exports/{slug}.md
```

Rejects content under 100 bytes or under half the original length, and asks for confirmation.
`--force` skips the confirmation prompt (non-interactive runs); the length checks still apply.

## Export

```bash
php artisan blog:manage export --id=10
```

Saves to `storage/app/blog-exports/{slug}.md`: a header (`---` lines holding slug, title, excerpt, category,
ai_generated, ai_model, series, part) above the content. Edit the content below the header; `create`
and `update` ignore the header, so edit title or excerpt with their flags or in the header before
sending to production.

## Send to production

`just post-to-prod <id>` (Bernard runs it: it writes to production). It exports the post and pipes it
to `blog:manage import` on the server, which matches by slug: a new slug becomes a **draft**, an
existing one is updated after a backup to `storage/app/blog-exports/backups/` (status unchanged).
Import refuses a series missing on the server (create it first), a part held by another post, and
content under half the current length without `--force` (`just post-to-prod <id> --force`). A header
without a series leaves the server post's series as it is.

## Series

```bash
php artisan blog:manage series create --slug=<slug> --title='<title>' --pitch='<one sentence>'
php artisan blog:manage series update --slug=<slug> ...
php artisan blog:manage series complete --slug=<slug>
```

A series shows on the site only once it has a published part.

## Publish

Bernard publishes in the admin UI at `/admin/blog`. Agents never publish. Publishing or unpublishing
rebuilds the sitemap.
