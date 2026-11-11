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

Saves to `storage/app/blog-exports/{slug}.md`. This file is also what goes to production.

## Series

```bash
php artisan blog:manage series create --slug=<slug> --title='<title>' --pitch='<one sentence>'
php artisan blog:manage series update --slug=<slug> ...
php artisan blog:manage series complete --slug=<slug>
```

A series shows on the site only once it has a published part.

## Publish

Bernard publishes in the admin UI at `/admin/blog`. Agents never publish.
