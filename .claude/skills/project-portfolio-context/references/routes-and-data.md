# Routes, tables and stores

Verified against the code 2026-11-11. The live sources win when they disagree with this file:
`php artisan route:list --except-vendor`, `resources/js/router/index.js`, `database/migrations/`,
`resources/js/stores/`, and `NotebookController` for the stamp rule (`route:list` cannot show it).

## Vue Router routes

```
/                     Home.vue
/about                About.vue
/projects             Projects.vue      ← static hardcoded project showcase, no DB table
/blog                 Blog.vue
/blog/series          BlogSeriesIndex.vue
/blog/series/:slug    BlogSeries.vue
/blog/:slug           BlogPost.vue
/uses                 Uses.vue
/contact              Contact.vue       ← static links only (GitHub, LinkedIn, email mailto:), no form, no DB table
/playground           Playground.vue
/login                Login.vue
/admin                AdminLayout.vue (requiresAuth: true)
  /admin/             admin/Dashboard.vue
  /admin/blog         admin/Blog.vue    ← all posts; a series post's row reads "· Part N of <series title>"
  /admin/blog/new     admin/BlogEditor.vue
  /admin/blog/:id/edit  admin/BlogEditor.vue
/:pathMatch(.*)*      NotFound.vue
```

## Laravel web routes (ahead of the SPA catch-all)

```
GET  /blog/{slug}, /blog/series, /blog/series/{slug}  → SPA shell with server-filled <title>, description, og: tags; 404 when missing
GET  /notebook/{stamp}   → NotebookController: static factory-notebook snapshot from resources/notebook/<stamp>.html
                           (stamp must match YYYY-MM-DD-HHMM, else 404); trailing slash optional
GET  /feed.xml           → RSS feed of published posts
GET  /login, POST /login (public); POST /logout (auth)
GET  /{any}              → SPA shell (catch-all, last)
```

A new notebook snapshot is a new file in `resources/notebook/` (copy with `file://` links unwrapped
and `<stamp>.html` links pointed at `/notebook/<stamp>/`); the published copies carry a marked
correction the originals lack, so never regenerate them without re-applying it
(`specs/module-18-factory-series.md`).

## API routes

**Public:**
```
GET  /api/blog          → paginated list of published posts
GET  /api/blog/{slug}   → single published post (markdown rendered to HTML); series posts add series_parts, previous/next by part
GET  /api/series        → public series: being written first, then complete, by latest part
GET  /api/series/{slug} → one series with its published parts (404 if none)
```
Posts in both blog endpoints carry `series` (slug, title, status, part, total) or null.

**Admin (auth:sanctum):**
```
GET    /api/admin/blog           → all posts (draft + published), each with its series and part
POST   /api/admin/blog           → create post
GET    /api/admin/blog/{id}      → get single post for editing
PUT    /api/admin/blog/{id}      → update post
DELETE /api/admin/blog/{id}      → delete post
PATCH  /api/admin/blog/{id}/publish  → toggle publish status
GET    /api/admin/me             → current user
POST   /api/admin/ai/generate    → stream blog draft (SSE)
```

**Guest (rate limited):**
```
GET  /api/playground/status     → guest usage status
POST /api/playground/generate   → stream content (SSE, 3/day limit)
POST /api/playground/key        → store guest API key in session
```

## Database tables

| Table | Purpose |
|-------|---------|
| `users` | Single admin user, no registration |
| `blog_posts` | All blog posts with draft/published status |
| `series` | Blog series: `slug`, `title`, `pitch`, `status` (writing/complete) |
| `guest_usage` | Persistence layer for guest playground usage |
| `ai_sessions` | Log of every AI generation (cost/usage monitoring) |

Plus the framework tables (`sessions`, `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `personal_access_tokens`).

**Key `blog_posts` columns:** `title`, `slug` (unique), `excerpt`, `content` (markdown), `status` (draft/published), `published_at`, `ai_generated` (bool), `ai_model`, `series_id` + `series_part` (nullable, unique pair)

**Series are data, not code:** `php artisan blog:manage series create|update|complete --slug=…`, and `--series=<slug> --part=<n>` on post create/update (omit `--part` to append; a taken part moves later parts up). Counts use published parts only.

## Pinia stores

```
stores/auth.js       → isAuthenticated, user; login(), logout(), fetchUser()
stores/theme.js      → isDark; toggle()
stores/playground.js → content, streaming, remaining, limitReached, hasOwnKey; fetchStatus(), generate(), submitKey()
stores/blog.js       → posts, currentPost, next/previousPost, seriesList, currentSeries, seriesParts, admin*; fetch*, create/update/deletePost(), togglePublish()
```
