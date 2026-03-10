# sinfat.com

Personal portfolio and technical blog by Bernard — full-stack developer based in Perth, Australia.

**Live:** [sinfat.com](https://sinfat.com)

---

## Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Vue 3 (Options API), Pinia, Tailwind CSS
- **AI:** Neuron AI with GitHub Models (GPT-4o-mini)
- **Database:** MySQL 8, Redis
- **Server:** Oracle Cloud Arm A1, Nginx, GitHub Actions auto-deploy
- **Design:** Terminal aesthetic — Geist Mono, dark-first, green accents

## Features

- Full SPA with Vue Router
- Markdown blog with syntax highlighting, reading time, RSS feed
- AI-powered blog post generation (SSE streaming)
- Guest AI playground with rate limiting
- Admin dashboard with markdown editor
- Dark/light mode toggle
- Per-route SEO meta tags and sitemap

## Development

```bash
# Install dependencies
composer install
npm install

# Run locally
php artisan serve
npm run dev

# Run tests
php artisan test
```

## Architecture

```
Laravel API (pure JSON) ← → Vue 3 SPA
         ↓
   Neuron AI Agents → GitHub Models / Anthropic / Gemini / Ollama
```

Controllers are thin. Business logic lives in Services. API output shaped by Resources. Blog content stored as markdown, rendered server-side via `Str::markdown()`.

## License

Private repository. All rights reserved.
