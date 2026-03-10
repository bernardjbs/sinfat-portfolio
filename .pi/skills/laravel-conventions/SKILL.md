---
name: laravel-conventions
description: Laravel coding conventions for sinfat-portfolio. Use when writing any PHP — controllers, services, models, migrations, middleware, API resources, or routes. Defines the Controller→Service→Resource pattern, naming rules, and what not to do.
---

# Laravel Conventions — sinfat-portfolio

Use this skill when building any Laravel backend code for this project. It defines how THIS project structures its PHP — not generic Laravel, but the specific patterns used here.

---

## Core Pattern: Controller → Service → Resource

Every API endpoint follows this chain:

```
Request → Controller (thin) → Service (logic) → Eloquent → Resource (shape output)
```

**Controllers** are thin — validate input, call service, return resource. No business logic.
**Services** contain all business logic — queries, transformations, AI calls, event dispatch.
**Resources** shape API output — never expose raw model attributes directly.

---

## Directory Structure

```
app/
  Http/
    Controllers/
      BlogController.php          ← public blog endpoints
      AdminBlogController.php     ← admin blog endpoints
      AdminAuthController.php     ← login/logout
      AiController.php            ← SSE streaming endpoints
      PlaygroundController.php    ← guest playground
    Middleware/
      GuestRateLimit.php          ← 3/day limit for playground
    Resources/
      BlogPostResource.php        ← shapes BlogPost for API
      BlogPostCollection.php      ← paginated list resource
  Models/
    User.php
    BlogPost.php
    GuestUsage.php
    AiSession.php
  Services/
    BlogService.php               ← blog CRUD logic
    AiService.php                 ← Neuron AI agent calls
    GuestUsageService.php         ← guest tracking logic
  Agents/
    BlogWriterAgent.php           ← Neuron AI agent definition
```

---

## Naming Conventions

| Thing | Convention | Example |
|-------|-----------|---------|
| Controllers | `NounController` or `AdminNounController` | `BlogController`, `AdminBlogController` |
| Services | `NounService` | `BlogService`, `AiService` |
| Resources | `NounResource` | `BlogPostResource` |
| Models | Singular PascalCase | `BlogPost`, `GuestUsage` |
| Migrations | `create_noun_table` | `create_blog_posts_table` |
| Middleware | PascalCase | `GuestRateLimit` |
| Agent classes | `NounAgent` | `BlogWriterAgent` |

---

## Route Structure

```php
// routes/web.php — only the SPA entry point
Route::get('/{any}', fn() => view('app'))->where('any', '.*');

// Auth (web middleware, session-based)
Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth');

// routes/api.php
Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{slug}', [BlogController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/me', [AdminAuthController::class, 'me']);
    Route::apiResource('/blog', AdminBlogController::class);
    Route::patch('/blog/{id}/publish', [AdminBlogController::class, 'togglePublish']);
    Route::post('/ai/generate', [AiController::class, 'generate']);
});

Route::middleware('guest-rate-limit')->prefix('playground')->group(function () {
    Route::post('/generate', [PlaygroundController::class, 'generate']);
    Route::post('/key', [PlaygroundController::class, 'setKey']);
});
```

---

## Controllers — Stay Thin

```php
class BlogController extends Controller
{
    public function __construct(private BlogService $blogService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $posts = $this->blogService->listPublished($request->integer('page', 1));
        return BlogPostResource::collection($posts);
    }

    public function show(string $slug): BlogPostResource
    {
        $post = $this->blogService->findBySlug($slug);
        return new BlogPostResource($post);
    }
}
```

---

## Validation — Form Requests

Use `php artisan make:request` for any endpoint with meaningful input:

```php
class StoreBlogPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'excerpt'      => ['nullable', 'string', 'max:500'],
            'status'       => ['required', 'in:draft,published'],
            'ai_generated' => ['boolean'],
            'ai_model'     => ['nullable', 'string'],
        ];
    }
}
```

---

## API Resources — Shape All Output

Never return `$model->toArray()` or `response()->json($model)` directly.

```php
class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->when($request->routeIs('api.blog.show'), $this->rendered_content),
            'status'       => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'ai_generated' => $this->ai_generated,
            'created_at'   => $this->created_at->toISOString(),
        ];
    }
}
```

---

## Models — Key Patterns

```php
class BlogPost extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'status',
                           'published_at', 'ai_generated', 'ai_model'];

    protected $casts = [
        'published_at' => 'datetime',
        'ai_generated' => 'boolean',
    ];

    // Auto-generate slug on create
    protected static function booted(): void
    {
        static::creating(function (BlogPost $post) {
            $post->slug ??= Str::slug($post->title);
        });
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    // Computed attribute — rendered markdown
    public function getRenderedContentAttribute(): string
    {
        return Str::markdown($this->content);
    }
}
```

---

## Middleware Registration

Register custom middleware in `bootstrap/app.php` (Laravel 12 style):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'guest-rate-limit' => GuestRateLimit::class,
    ]);
})
```

---

## Environment Variables

All config values go through `config/` files — never call `env()` directly in app code.

```php
// config/services.php
'ai' => [
    'provider' => env('AI_PROVIDER', 'anthropic'),
],
'github' => [
    'key'   => env('GITHUB_MODELS_KEY'),
    'model' => env('GITHUB_MODELS_MODEL', 'gpt-4o-mini'),
    'url'   => env('GITHUB_MODELS_URL', 'https://models.inference.ai.azure.com'),
],

// Usage in code
config('services.ai.provider')       ✅
config('services.github.key')        ✅
env('GITHUB_MODELS_KEY')             ❌ (outside config files)
```

---

## Error Handling

Return consistent JSON error shapes from all API endpoints:

```php
// 404
abort(404, 'Post not found');

// 401
return response()->json(['message' => 'Unauthenticated'], 401);

// 429 (rate limit)
return response()->json([
    'error'   => 'limit_reached',
    'message' => 'Free limit reached. Provide your Anthropic API key to continue.',
], 429);

// Validation errors — automatic from Form Requests (422)
```

---

## Testing Conventions

- PHPUnit for all backend tests
- Feature tests for API endpoints (use `RefreshDatabase`)
- Unit tests for Services and complex logic
- Test file mirrors app structure: `tests/Feature/BlogControllerTest.php`
- Run before every commit: `php artisan test`

```php
class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_posts_appear_in_listing(): void
    {
        $post = BlogPost::factory()->published()->create();
        $this->getJson('/api/blog')->assertOk()->assertJsonFragment(['slug' => $post->slug]);
    }
}
```

---

## Migration Conventions

- ❌ No `foreignId()` or `->constrained()` — never declare foreign key constraints at the DB level
- ✅ Use `unsignedBigInteger('x_id')` for any foreign-style column — raw column only
- Only add an index on a foreign-style column if the query pattern explicitly requires it

```php
// ✅ Correct
$table->unsignedBigInteger('user_id');

// ❌ Wrong
$table->foreignId('user_id')->constrained();
```

---

## What NOT to Do

- ❌ No Blade page templates — SPA only, only `resources/views/app.blade.php`
- ❌ No Breeze / Jetstream / Inertia
- ❌ No `env()` calls outside config files
- ❌ No business logic in controllers
- ❌ No raw `$model->toArray()` in API responses
- ❌ No direct DB calls in controllers — always go through a Service
- ❌ No `foreignId()` or `->constrained()` in migrations — use `unsignedBigInteger` only
