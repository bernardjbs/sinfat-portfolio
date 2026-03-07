---
name: test-writer
description: Testing conventions for sinfat-portfolio. Use when writing PHPUnit feature tests for any module. Defines what to test per layer, naming conventions, factory patterns, the actingAsAdmin helper, and what NOT to test.
---

# Test Writer — sinfat-portfolio

Use this skill when writing tests for any backend module. All tests are PHPUnit feature tests. No frontend testing. Tests live in `tests/Feature/` mirroring the controller structure.

---

## Non-Negotiable Rules

1. **`RefreshDatabase` on every test class** — never share state between tests
2. **Feature tests only for controllers** — unit tests only for complex standalone logic (e.g. a Service with tricky branching)
3. **One assertion cluster per test** — each test name describes one specific behaviour
4. **Test the API surface, not the implementation** — use `getJson`, `postJson`, etc. and assert on HTTP status + JSON shape
5. **Run `php artisan test` before every commit** — all 33+ tests must pass. Never commit with failures.

---

## File Naming

Mirror the controller:

```
app/Http/Controllers/BlogController.php          → tests/Feature/BlogControllerTest.php
app/Http/Controllers/AdminBlogController.php     → tests/Feature/AdminBlogControllerTest.php
app/Http/Controllers/GuestRateLimitTest.php      → tests/Feature/GuestRateLimitTest.php
```

---

## Test Method Naming

Snake case, descriptive, reads like a sentence:

```php
// ✅ Good — describes the behaviour
public function test_published_posts_appear_in_listing(): void
public function test_draft_posts_do_not_appear_in_listing(): void
public function test_unauthenticated_request_returns_401(): void
public function test_admin_can_publish_draft_post(): void

// ❌ Bad — vague, too short
public function test_index(): void
public function test_store(): void
```

---

## Class Template

```php
<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    // Group tests by endpoint using comments
    // GET /api/blog

    public function test_...: void
    {
        // arrange
        // act
        // assert
    }
}
```

---

## The `actingAsAdmin()` Helper

Every admin test class defines this private helper. Never repeat the User factory inline:

```php
private function actingAsAdmin(): static
{
    $admin = User::factory()->create();
    return $this->actingAs($admin, 'sanctum');
}
```

Use it on every admin request:
```php
$this->actingAsAdmin()->getJson('/api/admin/blog')->assertOk();
```

---

## Factory Patterns

`BlogPost` factory has named states. Use them:

```php
BlogPost::factory()->published()->create();   // status=published, published_at set
BlogPost::factory()->draft()->create();       // status=draft, published_at=null
BlogPost::factory()->count(5)->create();      // 5 posts with default state
```

Override specific fields inline when needed:
```php
BlogPost::factory()->create(['title' => 'Specific Title', 'slug' => 'specific-title']);
```

---

## What to Test Per Endpoint Type

### Public GET (listing)
```
✅ Returns 200 with correct data shape (assertJsonStructure)
✅ Only published posts appear (not drafts)
✅ Sensitive fields absent (status, ai_model)
✅ Content field absent from listing (present only on show)
✅ Pagination works (total, current_page, data count)
```

### Public GET (single resource)
```
✅ Returns 200 for valid slug/id
✅ Returns 404 for non-existent slug/id
✅ Returns 404 for draft post (not just published check)
✅ Content field present
✅ Sensitive fields absent
```

### Admin GET (listing + single)
```
✅ Returns 401 unauthenticated
✅ Returns 200 with correct data for admin (includes status, ai_model)
✅ Lists ALL posts including drafts (admin sees everything)
✅ Returns 404 for missing resource
```

### Admin POST (create)
```
✅ Returns 401 unauthenticated
✅ Returns 201 with created resource data
✅ Validation errors return 422 with correct field names
✅ Required fields enforced
✅ Auto-generated fields work (slug from title)
✅ assertDatabaseHas confirms record was created
```

### Admin PUT/PATCH (update)
```
✅ Returns 401 unauthenticated
✅ Returns 200 with updated data
✅ assertDatabaseHas confirms update persisted
✅ Validation errors return 422
✅ Returns 404 for missing resource
```

### Admin DELETE
```
✅ Returns 401 unauthenticated
✅ Returns 200 with confirmation message
✅ assertDatabaseMissing confirms record gone
✅ Returns 404 for missing resource
```

### Toggle actions (publish/unpublish)
```
✅ Draft → published: status becomes 'published', published_at is set
✅ Published → draft: status becomes 'draft', published_at is null
✅ assertDatabaseHas confirms both transitions
```

---

## Asserting JSON Shape

Use `assertJsonStructure` for the shape, `assertJsonPath` for specific values, `assertJsonFragment` for partial matches:

```php
// Shape (good for listing — confirms all expected keys exist)
->assertJsonStructure([
    'data' => [['id', 'title', 'slug', 'excerpt', 'published_at', 'ai_generated']],
    'meta' => ['current_page', 'last_page', 'total'],
])

// Specific value
->assertJsonPath('data.slug', 'my-post-slug')
->assertJsonPath('data.status', 'published')

// Field is present
$this->assertArrayHasKey('content', $response->json('data'));

// Field is absent (security check)
$this->assertArrayNotHasKey('status', $response->json('data.0'));
```

---

## Markdown Rendering Tests

When a controller renders markdown to HTML, test the output specifically:

```php
public function test_single_post_content_is_rendered_as_html(): void
{
    $post = BlogPost::factory()->published()->create([
        'content' => '## Hello World',
    ]);

    $response = $this->getJson("/api/blog/{$post->slug}")->assertOk();

    $this->assertStringContainsString('<h2>', $response->json('data.content'));
}
```

---

## What NOT to Test

- ❌ Laravel internals (routing, Eloquent ORM, migration structure)
- ❌ Implementation details (which method was called, how many DB queries ran)
- ❌ The factory itself (it's a test tool, not app code)
- ❌ Trivial getters/setters with no logic
- ❌ Frontend behaviour — no browser tests, no JS tests
- ❌ Things already tested by an existing test — don't duplicate

---

## Running Tests

```bash
php artisan test                    # all tests
php artisan test --filter Blog      # tests matching "Blog"
php artisan test --stop-on-failure  # halt on first failure
```

Always run the full suite before committing. A passing subset with a broken full suite is not acceptable.
