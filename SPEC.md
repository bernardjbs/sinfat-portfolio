# sinfat.com — Implementation Spec
> Full-stack portfolio site: Laravel 12 API + Vue 3 SPA
> Reference: PORTFOLIO.md in pi-vs-claude-code for decisions and reasoning

---

## How to Use This Document

Each module is self-contained and ordered by dependency. Complete modules sequentially.
Each module has:
- **Goal** — what it delivers
- **Tasks** — ordered, specific
- **Technical detail** — schema / API / components
- **Acceptance criteria** — how you know it's done
- **Dependencies** — what must be done first

**Model recommendation per module:**
- 🟢 Sonnet — structured implementation, known inputs
- 🔴 Opus — complex reasoning, schema design, API contracts

---

## Module 1 — Infrastructure & Server Setup
> 🟢 Sonnet

### Goal
Production server fully configured and ready to receive the application.

### Tasks
- [ ] Install Redis on VM
- [ ] Configure Redis in Laravel `.env`
- [ ] Install Let's Encrypt SSL (Cloudflare Full mode)
- [ ] Set up GitHub Actions for automated deploy on push to main
- [ ] Install Supervisor for queue workers
- [ ] Record Oracle VM public IP in this file

### Technical Detail

**Redis install:**
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

**Laravel `.env` (production):**
```
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Let's Encrypt:**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d sinfat.com -d www.sinfat.com
```

**GitHub Actions deploy (`.github/workflows/deploy.yml`):**
```yaml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Oracle VM
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ubuntu
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            cd /var/www/sinfat
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan sitemap:generate
```

**Supervisor config (`/etc/supervisor/conf.d/sinfat-worker.conf`):**
```ini
[program:sinfat-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sinfat/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=1
user=ubuntu
```

**Oracle VM public IP:** `<FILL IN>`

### Acceptance Criteria
- [ ] `redis-cli ping` returns `PONG`
- [ ] `https://sinfat.com` loads with valid SSL (padlock in browser)
- [ ] Push to main triggers GitHub Action and deploys automatically
- [ ] Supervisor running: `sudo supervisorctl status`

### Dependencies
None — do this first.

---

## Module 2 — Database Schema
> 🔴 Opus — review schema carefully before running migrations

### Goal
All database tables designed, migrated, and verified.

### Tasks
- [ ] Design and create all migrations
- [ ] Create all Eloquent models with relationships
- [ ] Seed admin user
- [ ] Verify schema in TablePlus

### Schema

**users**
```
id                  bigint PK
name                varchar(255)
email               varchar(255) unique
password            varchar(255)
remember_token      varchar(100) nullable
created_at          timestamp
updated_at          timestamp
```
Note: Single admin user only. No registration route.

**blog_posts**
```
id                  bigint PK
title               varchar(255)
slug                varchar(255) unique
excerpt             text nullable
content             longtext          ← markdown stored as-is
status              enum('draft','published')  default: draft
published_at        timestamp nullable
ai_generated        boolean default: false    ← was this AI drafted?
ai_model            varchar(100) nullable      ← which model drafted it
created_at          timestamp
updated_at          timestamp

Indexes:
- slug (unique)
- status + published_at (for public listing query)
```

**guest_usage**
```
id                  bigint PK
identifier          varchar(255)      ← IP address or session fingerprint
type                enum('ip','session')
count               tinyint default: 0
last_used_at        timestamp
expires_at          timestamp         ← reset after 24 hours
created_at          timestamp
updated_at          timestamp

Indexes:
- identifier + type (unique composite)
- expires_at (for cleanup)
```
Note: Redis handles runtime rate limiting. This table is for persistence and analytics only.

**ai_sessions**
```
id                  bigint PK
identifier          varchar(255)      ← guest identifier or 'admin'
type                enum('guest','admin')
topic               text
model               varchar(100)
tokens_used         int nullable
status              enum('pending','streaming','completed','failed')
created_at          timestamp
updated_at          timestamp
```
Note: Logs every AI generation attempt. Useful for monitoring cost and usage patterns.

### Models

```
User          → hasMany(BlogPost) [future]
BlogPost      → no relations for now
GuestUsage    → standalone
AiSession     → standalone
```

### Seeders

```php
// AdminSeeder — creates the single admin user
User::create([
    'name'     => 'Bernard',
    'email'    => env('ADMIN_EMAIL'),
    'password' => Hash::make(env('ADMIN_PASSWORD')),
]);
```

Run: `php artisan db:seed --class=AdminSeeder`

Add to `.env`:
```
ADMIN_EMAIL=your@email.com
ADMIN_PASSWORD=your-secure-password
```

### Acceptance Criteria
- [ ] All migrations run without errors: `php artisan migrate`
- [ ] All tables visible in TablePlus with correct columns
- [ ] Admin seeder creates user: `php artisan db:seed --class=AdminSeeder`
- [ ] Admin user can log in (Module 3)

### Dependencies
Module 1 (Redis configured in `.env`)

---

## Module 3 — Authentication
> 🟢 Sonnet

### Goal
Admin login/logout working. `/admin` routes protected. Terminal-styled login page.

### Tasks
- [ ] Create `AdminAuthController`
- [ ] Create auth routes
- [ ] Create `auth` middleware guard (already in Laravel)
- [ ] Build `Login.vue` component — terminal aesthetic
- [ ] Test login/logout flow

### Technical Detail

**Routes (`routes/web.php`):**
```php
Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth');
```

**Routes (`routes/api.php`):**
```php
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // blog routes — Module 5
    // ai routes — Module 6
});
```

**AdminAuthController:**
```php
public function show() {
    return view('app'); // SPA entry point
}

public function login(Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return response()->json(['redirect' => '/admin']);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
}

public function logout(Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['redirect' => '/']);
}
```

**Login.vue — terminal aesthetic:**
```
Dark background (#0d1117)
Monospace font (Geist Mono)
Simple form: email + password + submit
Subtle green accent on focus/active states
No logo, no hero — just the form, centred
Error state: dim red text below field
```

### Acceptance Criteria
- [ ] `GET /login` serves the SPA
- [ ] `POST /login` with valid credentials returns 200 + redirect
- [ ] `POST /login` with invalid credentials returns 401 + error message
- [ ] Visiting `/admin` when unauthenticated redirects to `/login`
- [ ] Login page matches terminal aesthetic
- [ ] Logout clears session

### Dependencies
Module 2 (admin user seeded)

---

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

## Module 6 — AI Integration (SSE + Neuron AI)
> 🔴 Opus — agent configuration and streaming implementation

### Goal
Admin can trigger an AI agent to draft a blog post. Draft streams token by token into the editor. Admin can chat with the model to refine before saving.

### Tasks
- [ ] Install Neuron AI: `composer require inspector-apm/neuron-ai`
- [ ] Configure Anthropic API key in `.env`
- [ ] Build `AiController` with SSE streaming endpoint
- [ ] Build `BlogWriterAgent` using Neuron AI
- [ ] Build streaming chat interface in `AdminBlogEditor.vue`
- [ ] Log AI sessions to `ai_sessions` table

### Technical Detail

**`.env`:**
```
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_MODEL=claude-sonnet-4-5
```

**BlogWriterAgent:**
```php
use NeuronAI\Agent;
use NeuronAI\Providers\Anthropic;

class BlogWriterAgent extends Agent {
    protected function provider(): Anthropic {
        return new Anthropic(
            apiKey: config('services.anthropic.key'),
            model: config('services.anthropic.model'),
        );
    }

    protected function instructions(): string {
        return "You are an expert technical blog writer specialising in Laravel, 
                Vue, and AI development. Write clear, practical, developer-focused 
                content. Use markdown formatting. Be opinionated and specific.";
    }
}
```

**AiController — SSE streaming:**
```php
public function generate(Request $request) {
    $topic = $request->validated()['topic'];

    return response()->stream(function () use ($topic) {
        $agent = new BlogWriterAgent();

        $agent->stream("Write a blog post about: {$topic}")
            ->each(function ($chunk) {
                echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                ob_flush();
                flush();
            });

        echo "data: [DONE]\n\n";
        ob_flush();
        flush();
    }, 200, [
        'Content-Type'  => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no', // Nginx: disable buffering
    ]);
}
```

**Vue streaming — composable:**
```javascript
// useStream.js
export function useStream(url) {
    const content = ref('')
    const streaming = ref(false)

    function start(payload) {
        streaming.value = true
        content.value = ''

        const source = new EventSource(url)
        source.onmessage = (event) => {
            if (event.data === '[DONE]') {
                streaming.value = false
                source.close()
                return
            }
            content.value += JSON.parse(event.data).text
        }
        source.onerror = () => {
            streaming.value = false
            source.close()
        }
    }

    return { content, streaming, start }
}
```

### Acceptance Criteria
- [ ] Admin clicks "Generate" → content streams into editor token by token
- [ ] `[DONE]` signal stops the stream cleanly
- [ ] Streamed content lands in md-editor-v3 and is editable immediately
- [ ] AI session logged to `ai_sessions` table
- [ ] Nginx buffering disabled (`X-Accel-Buffering: no`)

### Dependencies
Modules 3, 4, 5

---

## Module 7 — Guest Playground
> 🔴 Opus — rate limiting logic and security review

### Goal
Guests can generate blog content via AI. 3 free generations per day. After limit, prompted for their own Anthropic API key. Keys never stored — session only.

### Tasks
- [ ] `GuestRateLimit` middleware
- [ ] `PlaygroundController` with SSE endpoint
- [ ] Guest key — session storage only
- [ ] `Playground.vue` — chat interface with counter
- [ ] API key modal — appears after limit reached
- [ ] Log guest usage to `guest_usage` table

### Technical Detail

**GuestRateLimit middleware:**
```php
public function handle(Request $request, Closure $next) {
    $key = 'playground:' . $request->ip();

    if (RateLimiter::tooManyAttempts($key, 3)) {
        // Check if guest provided their own key
        if (!$request->session()->has('guest_api_key')) {
            return response()->json([
                'error'   => 'limit_reached',
                'message' => 'Free limit reached. Please provide your Anthropic API key.'
            ], 429);
        }
    }

    RateLimiter::hit($key, 86400); // 24 hour decay
    return $next($request);
}
```

**Guest API key — session only:**
```php
// Store guest key in session — never in DB, never in response
public function setKey(Request $request) {
    $request->validate(['api_key' => 'required|string|starts_with:sk-ant-']);
    $request->session()->put('guest_api_key', $request->api_key);
    return response()->json(['success' => true]);
}

// Use guest key if available, fall back to system key
$apiKey = $request->session()->get('guest_api_key', config('services.anthropic.key'));
```

**Playground.vue layout:**
```
Header: "AI Blog Playground" | counter badge "3 free generations remaining"
Input: text field "What would you like to write about?"
Output: streaming text area — tokens appear as they arrive
Actions: Generate | Copy | Clear
Modal: appears when limit reached — "Enter your Anthropic API key to continue"
```

### Acceptance Criteria
- [ ] Guest generates content without auth
- [ ] Counter decrements with each generation
- [ ] After 3 uses, modal appears prompting for API key
- [ ] Guest API key accepted and stored in session only
- [ ] Guest API key used for subsequent generations
- [ ] Key never appears in API response or frontend state
- [ ] Rate limit resets after 24 hours
- [ ] All generations logged to `ai_sessions` and `guest_usage`

### Dependencies
Modules 4, 6

---

## Module 8 — Frontend SPA Foundation
> 🟢 Sonnet

### Goal
Vue SPA scaffolded with routing, Pinia stores, layout components, and design system in place.

### Tasks
- [ ] Install and configure Vue Router
- [ ] Install and configure Pinia
- [ ] Install Lucide Vue: `npm install lucide-vue-next`
- [ ] Install md-editor-v3: `npm install md-editor-v3`
- [ ] Configure Geist Mono font
- [ ] Configure Tailwind with terminal palette + typography plugin
- [ ] Build `AppLayout.vue` — nav, footer, dark/light toggle
- [ ] Build `AdminLayout.vue` — sidebar, admin nav
- [ ] Set up all routes in `router/index.js`
- [ ] Set up Pinia stores

### Technical Detail

**Tailwind config:**
```js
// tailwind.config.js
export default {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                mono: ['Geist Mono', 'monospace'],
            },
            colors: {
                bg:      '#0d1117',
                surface: '#161b22',
                accent:  '#238636',
                text:    '#e6edf3',
                dim:     '#6e7681',
                border:  '#30363d',
            }
        }
    },
    plugins: [require('@tailwindcss/typography')],
}
```

**Geist Mono font (`resources/css/app.css`):**
```css
@import url('https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600&display=swap');
```

**Pinia stores:**
```
stores/
    auth.js        → isAuthenticated, user, login(), logout()
    theme.js       → isDark, toggle()
    playground.js  → usageCount, isStreaming, content, apiKey
    blog.js        → posts, currentPost, loading
```

**Vue Router routes:**
```js
const routes = [
    { path: '/',              component: () => import('./pages/Home.vue') },
    { path: '/about',         component: () => import('./pages/About.vue') },
    { path: '/projects',      component: () => import('./pages/Projects.vue') },
    { path: '/blog',          component: () => import('./pages/Blog.vue') },
    { path: '/blog/:slug',    component: () => import('./pages/BlogPost.vue') },
    { path: '/uses',          component: () => import('./pages/Uses.vue') },
    { path: '/contact',       component: () => import('./pages/Contact.vue') },
    { path: '/playground',    component: () => import('./pages/Playground.vue') },
    { path: '/login',         component: () => import('./pages/Login.vue') },
    {
        path: '/admin',
        component: () => import('./layouts/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '',       component: () => import('./pages/admin/Dashboard.vue') },
            { path: 'blog',   component: () => import('./pages/admin/Blog.vue') },
            { path: 'blog/:id', component: () => import('./pages/admin/BlogEditor.vue') },
        ]
    },
    { path: '/:pathMatch(.*)*', component: () => import('./pages/NotFound.vue') },
]
```

**Auth guard:**
```js
router.beforeEach((to, from, next) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login')
    } else {
        next()
    }
})
```

### Acceptance Criteria
- [ ] `https://sinfat.test` loads Vue SPA
- [ ] All routes navigate without page reload
- [ ] Dark/light toggle works and persists in localStorage
- [ ] Unauthenticated visit to `/admin` redirects to `/login`
- [ ] Geist Mono font rendering in browser
- [ ] Tailwind palette applied — bg colour matches `#0d1117`
- [ ] Pinia stores initialised

### Dependencies
Module 3 (auth guard needs auth store)

---

## Module 9 — Static Pages
> 🟢 Sonnet

### Goal
All public-facing pages built with content and terminal aesthetic.

### Pages

**Home.vue**
```
Hero: name + one-liner headline
      "Full-stack developer (Laravel + Vue) — helping teams integrate AI responsibly."
CTA: Download CV button + Contact link
Brief: 3-line intro
Featured: 2-3 project cards
Latest: 2-3 blog post previews
```

**About.vue**
```
Professional summary (no employer name — "Perth-based SaaS company since 2022")
Education: ECU CS degree + UWA certificate
Skills: grouped by category (Backend, Frontend, AI, Testing)
Philosophy: short paragraph on how you work
CV download button
```

**Projects.vue**
```
pi-agent-toolkit    → GitHub link + description + tech tags
Football Analytics  → description + honest story (5 iterations) — no live link
Time-Capsule App    → description + GitHub if public
This site           → GitHub link + tech used
```

**Uses.vue**
```
TBD — scan /code directory when ready to build
```

**Contact.vue**
```
GitHub link
LinkedIn link
Email link (mailto)
Location: Perth, Australia
Availability: Open to local roles with WFH flexibility
```

**Blog.vue**
```
Post list — title, excerpt, date, ai_generated badge
Pagination
```

**BlogPost.vue**
```
Title + date + ai_generated badge
Rendered markdown with prose styling
Back to blog link
```

### Acceptance Criteria
- [ ] All pages render correctly at their routes
- [ ] Terminal aesthetic consistent across all pages
- [ ] CV downloads from About + Home
- [ ] All external links open in new tab
- [ ] Mobile responsive

### Dependencies
Module 8

---

## Module 10 — Sitemap + SEO
> 🟢 Sonnet

### Goal
Sitemap generated, meta tags per page, robots.txt in place.

### Tasks
- [ ] Install `spatie/laravel-sitemap`: `composer require spatie/laravel-sitemap`
- [ ] Create `GenerateSitemap` artisan command
- [ ] Add meta tags per route (title, description, og:image)
- [ ] Create `public/robots.txt`
- [ ] Add sitemap generation to deploy pipeline (Module 1)
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
- [ ] `https://sinfat.com/sitemap.xml` accessible
- [ ] All published blog posts included in sitemap
- [ ] `robots.txt` blocks `/admin` and `/login`
- [ ] Each page has correct `<title>` and `<meta description>`

### Dependencies
Module 5 (blog posts in sitemap)

---

## Module 11 — Deploy Pipeline Polish
> 🟢 Sonnet

### Goal
Deployment is automated, reliable, and zero-downtime where possible.

### Tasks
- [ ] Verify GitHub Actions deploy works end to end
- [ ] Add `.env.production` documentation (what keys are required)
- [ ] Add `php artisan sitemap:generate` to deploy script
- [ ] Test full deploy: push → action triggers → site updates
- [ ] Document manual deploy fallback

### Required Production `.env` Keys
```
APP_NAME=sinfat
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sinfat.com
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sinfat_portfolio
DB_USERNAME=sinfat
DB_PASSWORD=

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

ANTHROPIC_API_KEY=
ANTHROPIC_MODEL=claude-sonnet-4-5

ADMIN_EMAIL=
ADMIN_PASSWORD=
```

### Manual Deploy Fallback
```bash
ssh -i ~/.ssh/sinfat-portfolio.key ubuntu@<oracle-ip>
cd /var/www/sinfat
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan sitemap:generate
sudo systemctl reload nginx
```

### Acceptance Criteria
- [ ] Push to main → GitHub Action → live site updated within 2 minutes
- [ ] All `.env` keys documented
- [ ] Manual deploy fallback documented and tested
- [ ] `https://sinfat.com` serving latest code after deploy

### Dependencies
All modules

---

## Build Order Summary

```
Module 1  → Infrastructure (do first — foundation)
Module 2  → Database Schema 🔴
Module 3  → Authentication
Module 4  → API Contract 🔴
Module 8  → Frontend SPA Foundation (parallel with 3/4)
Module 5  → Blog Backend + Frontend
Module 6  → AI Integration 🔴
Module 7  → Guest Playground 🔴
Module 9  → Static Pages
Module 10 → Sitemap + SEO
Module 11 → Deploy Pipeline Polish
```

🔴 = Use Opus for these modules

---

## Pi Workflow Reference

**Skills to load:** `portfolio-context` · `laravel-conventions` · `vue-conventions` · `terminal-aesthetic` · `neuron-ai-patterns` · `git-conventions`

**Agents available:** `backend-developer` · `frontend-developer` · `database-architect` · `security-reviewer` · `test-writer` · `git-assistant` · `pr-reviewer`

**Per-feature pattern:**
```
git checkout -b feat/module-X-description
→ dispatch relevant agent(s)
→ review + test
→ dispatch git-assistant → commit
→ dispatch pr-reviewer → merge
→ push to main → auto-deploy
```

---

*Created: 2026-03-06*
*Status: Planning complete — ready to build*
