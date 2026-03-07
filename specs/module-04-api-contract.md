## Module 4 — API Contract
> 🔴 Opus — define carefully, everything else depends on this

### Goal
Full API surface defined. All endpoints, request shapes, response shapes, middleware.

### Blog API

```
GET    /api/blog                    → public, list published posts (paginated)
GET    /api/blog/{slug}             → public, single post
GET    /api/admin/blog              → auth, list all posts (draft + published)
POST   /api/admin/blog              → auth, create post
GET    /api/admin/blog/{id}         → auth, get single post for editing
PUT    /api/admin/blog/{id}         → auth, update post
DELETE /api/admin/blog/{id}         → auth, delete post
PATCH  /api/admin/blog/{id}/publish → auth, toggle publish status
```

**GET /api/blog response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Post title",
      "slug": "post-title",
      "excerpt": "Short description...",
      "published_at": "2026-03-06T00:00:00Z",
      "ai_generated": true
    }
  ],
  "meta": { "current_page": 1, "last_page": 3, "total": 12 }
}
```

**GET /api/blog/{slug} response:**
```json
{
  "data": {
    "id": 1,
    "title": "Post title",
    "slug": "post-title",
    "content": "<rendered HTML>",
    "excerpt": "Short description...",
    "published_at": "2026-03-06T00:00:00Z",
    "ai_generated": true
  }
}
```

**POST /api/admin/blog request:**
```json
{
  "title": "Post title",
  "content": "## Markdown content...",
  "excerpt": "Optional short description",
  "status": "draft",
  "ai_generated": false,
  "ai_model": null
}
```

### AI API

```
POST   /api/admin/ai/generate       → auth, stream blog draft (SSE)
POST   /api/playground/generate     → guest, stream content (SSE, rate limited)
```

**POST /api/admin/ai/generate request:**
```json
{
  "topic": "How I secured an AI feature in Laravel",
  "context": "Optional additional context or notes"
}
```

**POST /api/playground/generate request:**
```json
{
  "topic": "Any topic the guest enters",
  "api_key": "sk-ant-..." // optional — guest's own key after free limit
}
```

Both return `text/event-stream` — SSE streaming response.

### Auth API

```
POST   /login                       → public
POST   /logout                      → auth
GET    /api/admin/me                → auth, returns current user
```

### Middleware Map

```
/api/blog/*              → public
/api/admin/*             → auth:sanctum
/api/playground/*        → GuestRateLimit middleware
```

### Acceptance Criteria
- [ ] All routes registered: `php artisan route:list`
- [ ] Public blog endpoints return correct JSON without auth
- [ ] Admin endpoints return 401 without auth token
- [ ] Playground endpoint rate limits after 3 requests per IP per day

### Dependencies
Modules 2, 3

---

