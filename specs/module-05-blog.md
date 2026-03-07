## Module 5 — Blog (Admin + Public)
> 🟢 Sonnet

### Goal
Admin can create, edit, publish, and delete blog posts. Public can browse and read published posts.

### Tasks

**Backend:**
- [x] `BlogController` (public)
- [x] `AdminBlogController` (protected)
- [x] `BlogPost` Resource (API response shaping)
- [x] Markdown → HTML rendering via `Str::markdown()` (league/commonmark, already installed)
- [x] Slug auto-generation from title

**Frontend:**
- [x] `Blog.vue` — public post listing, paginated
- [x] `BlogPost.vue` — single post with rendered HTML + `prose` styling
- [x] `AdminBlog.vue` — post list with status, edit/delete/publish actions
- [x] `AdminBlogEditor.vue` — md-editor-v3 + save/publish controls

### Technical Detail

**Slug generation:**
```php
// In BlogPost model boot()
static::creating(function ($post) {
    $post->slug = Str::slug($post->title);
});
```

**Markdown rendering:**
```php
// In BlogPostResource — public show only
'content' => $this->when(
    $request->routeIs('api.blog.show'),
    fn() => Str::markdown((string) $this->content)
),
// Admin show returns raw markdown for the editor
'raw_content' => $this->when(
    $request->routeIs('api.admin.blog.show'),
    $this->content
),
```

**AdminBlogEditor.vue layout:**
```
Top bar: title input | status badge | Save Draft | Publish buttons
Body: md-editor-v3 (full height, dark mode)
Bottom: excerpt input | metadata (created, model used)
```

### Acceptance Criteria
- [x] Admin can create a post with title + markdown content
- [x] Slug auto-generated from title
- [x] Draft posts not visible on public blog
- [x] Published posts appear on `/blog` listing
- [x] Single post at `/blog/:slug` renders markdown as HTML with prose styling
- [x] Admin can edit, publish, unpublish, delete posts

### Dependencies
Modules 3, 4

---

