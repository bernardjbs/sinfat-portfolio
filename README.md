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
- **Dev workflow:** [pi](https://github.com/mariozechner/pi) coding agent with custom skills, agents, and module runner

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

## Development Process

This project was built using [pi](https://github.com/mariozechner/pi), a coding agent, with a structured module-by-module workflow. The `.pi/` directory contains the skills, agents, and session state that guided the build — 12 modules from infrastructure to deploy pipeline, each following a Plan → Execute → Test → Document cycle.

See `docs/sessions/` for per-module notes and `specs/` for the original module specs.

## License

Source code is public for reference and learning. Not licensed for redistribution or commercial use.
