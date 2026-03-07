## Module 5 — Blog (Admin + Public)
> 🟢 Sonnet

### Goal
Admin can create, edit, publish, and delete blog posts. Public can browse and read published posts.

### Tasks

**Backend:**
- [ ] `BlogController` (public)
- [ ] `AdminBlogController` (protected)
- [ ] `BlogPost` Resource (API response shaping)
- [ ] Markdown → HTML rendering via `spatie/laravel-markdown`
- [ ] Slug auto-generation from title

**Frontend:**
- [ ] `Blog.vue` — public post listing, paginated
- [ ] `BlogPost.vue` — single post with rendered HTML + `prose` styling
- [ ] `AdminBlog.vue` — post list with status, edit/delete/publish actions
- [ ] `AdminBlogEditor.vue` — md-editor-v3 + save/publish controls

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
// In BlogController
$post->content = Str::markdown($post->content);
```

**AdminBlogEditor.vue layout:**
```
Top bar: title input | status badge | Save Draft | Publish buttons
Body: md-editor-v3 (full height, dark mode)
Bottom: excerpt input | metadata (created, model used)
```

### Acceptance Criteria
- [ ] Admin can create a post with title + markdown content
- [ ] Slug auto-generated from title
- [ ] Draft posts not visible on public blog
- [ ] Published posts appear on `/blog` listing
- [ ] Single post at `/blog/:slug` renders markdown as HTML with prose styling
- [ ] Admin can edit, publish, unpublish, delete posts

### Dependencies
Modules 3, 4

---

