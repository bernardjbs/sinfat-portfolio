<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-off data move (module 17): the 14 "Building sinfat.com" posts become parts 1 to 14 of one
 * series, with the titles and excerpts approved 2026-11-10. Matches posts by slug, never id or date,
 * and never touches slugs, content, published dates or status.
 */
class MoveBuildingSinfat extends Command
{
    protected $signature = 'blog:move-building-sinfat
        {--apply : Write the changes (without it, a dry run that writes nothing)}';

    protected $description = 'Move the 14 Building sinfat.com posts into their series (dry run unless --apply)';

    private const SERIES = [
        'slug'   => 'building-sinfat',
        'title'  => 'Building sinfat.com with AI',
        'pitch'  => 'How this site went from a plan to production with AI agents, one module at a time.',
        'status' => 'complete',
    ];

    // title and excerpt: [old, approved]; posts without an excerpt row keep theirs
    private const PARTS = [
        [
            'part'    => 1,
            'slug'    => '01-building-sinfatcom-a-developer-portfolio-built-with-ai',
            'title'   => ['01 — Building sinfat.com — A Developer Portfolio Built With AI', 'Building sinfat.com: A Developer Portfolio Built With AI'],
        ],
        [
            'part'    => 2,
            'slug'    => '02-learning-pi-what-12-modules-taught-me-about-ai-coding-agents',
            'title'   => ['02 — Learning Pi — What 12 Modules Taught Me About AI Coding Agents', 'Learning Pi: What 12 Modules Taught Me About AI Coding Agents'],
        ],
        [
            'part'    => 3,
            'slug'    => '03-i-spent-a-full-day-planning-before-writing-a-single-line-of-code',
            'title'   => ['03 — I Spent a Full Day Planning Before Writing a Single Line of Code', 'I Spent a Full Day Planning Before Writing a Single Line of Code'],
            'excerpt' => [
                'Skills, agents, and a module runner workflow — why I scaffolded the AI development process before touching application code.',
                'Skills, agents, and a module runner workflow: why I scaffolded the AI development process before touching application code.',
            ],
        ],
        [
            'part'    => 4,
            'slug'    => '04-setting-up-an-oracle-cloud-arm-vm-for-laravel-five-roadblocks-and-how-ai-helped',
            'title'   => ['04 — Setting Up an Oracle Cloud Arm VM for Laravel — Five Roadblocks and How AI Helped', 'Setting Up an Oracle Cloud Arm VM for Laravel: Five Roadblocks and How AI Helped'],
            'excerpt' => [
                'Oracle Cloud free tier, Cloudflare, Nginx, PHP-FPM, MySQL — and the five roadblocks I hit getting it all working in one session.',
                'Oracle Cloud free tier, Cloudflare, Nginx, PHP-FPM, MySQL, and the five roadblocks I hit getting it all working in one session.',
            ],
        ],
        [
            'part'    => 5,
            'slug'    => '05-database-schema-decisions-that-saved-me-later',
            'title'   => ['05 — Database Schema Decisions That Saved Me Later', 'Database Schema Decisions That Saved Me Later'],
            'excerpt' => [
                'Why I dropped a table, skipped foreign keys, and used a string column instead of a pivot table — deliberate schema decisions for a solo project.',
                'Why I dropped a table, skipped foreign keys, and used a string column instead of a pivot table: deliberate schema decisions for a solo project.',
            ],
        ],
        [
            'part'    => 6,
            'slug'    => '06-session-auth-in-a-vue-spa-sanctum-csrf-and-the-refresh-problem',
            'title'   => ['06 — Session Auth in a Vue SPA — Sanctum, CSRF, and the Refresh Problem', 'Session Auth in a Vue SPA: Sanctum, CSRF, and the Refresh Problem'],
            'excerpt' => [
                'Building session-based authentication in a Vue SPA — the refresh problem, why Sanctum exists, and how CSRF tokens work with cookies.',
                'Building session-based authentication in a Vue SPA: the refresh problem, why Sanctum exists, and how CSRF tokens work with cookies.',
            ],
        ],
        [
            'part'    => 7,
            'slug'    => '07-define-the-api-before-you-build-it',
            'title'   => ['07 — Define the API Before You Build It', 'Define the API Before You Build It'],
            'excerpt' => [
                'Twelve endpoints locked in before any business logic — why defining the API contract first made every subsequent module simpler.',
                'Twelve endpoints locked in before any business logic, and why defining the API contract first made every subsequent module simpler.',
            ],
        ],
        [
            'part'    => 8,
            'slug'    => '08-sse-streaming-with-neuron-ai-in-laravel-the-nginx-buffering-trap',
            'title'   => ['08 — SSE Streaming with Neuron AI in Laravel — The Nginx Buffering Trap', 'SSE Streaming with Neuron AI in Laravel: The Nginx Buffering Trap'],
            'excerpt' => [
                'Building AI streaming into a Laravel app — why SSE over WebSockets, the Nginx buffering header nobody tells you about, and switching LLM providers with one env var.',
                'Building AI streaming into a Laravel app: why SSE over WebSockets, the Nginx buffering header nobody tells you about, and switching LLM providers with one env var.',
            ],
        ],
        [
            'part'    => 9,
            'slug'    => '09-building-a-guest-ai-playground-rate-limiting-session-keys-and-three-bugs',
            'title'   => ['09 — Building a Guest AI Playground — Rate Limiting, Session Keys, and Three Bugs', 'Building a Guest AI Playground: Rate Limiting, Session Keys, and Three Bugs'],
        ],
        [
            'part'    => 10,
            'slug'    => '10-terminal-aesthetic-designing-a-developer-portfolio-that-doesnt-look-like-every-other-one',
            'title'   => ['10 — Terminal Aesthetic — Designing a Developer Portfolio That Doesn\'t Look Like Every Other One', 'Terminal Aesthetic: Designing a Developer Portfolio That Doesn\'t Look Like Every Other One'],
        ],
        [
            'part'    => 11,
            'slug'    => '11-seo-for-a-single-page-app-with-laravel',
            'title'   => ['11 — SEO for a Single-Page App with Laravel', 'SEO for a Single-Page App with Laravel'],
        ],
        [
            'part'    => 12,
            'slug'    => '12-from-github-actions-to-just-deploy-simplifying-production-deploys',
            'title'   => ['12 — From GitHub Actions to just deploy — Simplifying Production Deploys', 'From GitHub Actions to just deploy: Simplifying Production Deploys'],
        ],
        [
            'part'    => 13,
            'slug'    => '13-when-your-ai-session-crashes-mid-module',
            'title'   => ['13 — When Your AI Session Crashes Mid-Module', 'When Your AI Session Crashes Mid-Module'],
        ],
        [
            'part'    => 14,
            'slug'    => '14-the-repo-is-the-portfolio-why-i-kept-it-private-until-it-was-done',
            'title'   => ['14 — The Repo Is the Portfolio — Why I Kept It Private Until It Was Done', 'The Repo Is the Portfolio: Why I Kept It Private Until It Was Done'],
        ],
    ];

    public function handle(): int
    {
        $slugs = array_column(self::PARTS, 'slug');
        $posts = BlogPost::whereIn('slug', $slugs)->get()->keyBy('slug');

        $missing = array_diff($slugs, $posts->keys()->all());

        if ($missing) {
            $this->error('Missing posts, nothing written: ' . implode(', ', $missing));
            return 1;
        }

        $series = Series::where('slug', self::SERIES['slug'])->first();
        $problems = [];
        $changes = [];

        if (! $series) {
            $changes[] = 'create series ' . self::SERIES['slug'] . ' (' . self::SERIES['status'] . ')';
        } else {
            foreach (['title', 'pitch', 'status'] as $field) {
                if ($series->{$field} !== self::SERIES[$field]) {
                    $changes[] = "series {$field}: {$series->{$field}} → " . self::SERIES[$field];
                }
            }

            if (BlogPost::where('series_id', $series->id)->whereNotIn('slug', $slugs)->exists()) {
                $problems[] = 'another post is already in ' . self::SERIES['slug'];
            }
        }

        foreach (self::PARTS as $row) {
            $post = $posts[$row['slug']];
            $label = "part {$row['part']} ({$row['slug']})";

            foreach (['title', 'excerpt'] as $field) {
                if (! isset($row[$field])) {
                    continue;
                }

                [$old, $new] = $row[$field];

                if (! in_array($post->{$field}, [$old, $new], true)) {
                    $problems[] = "{$label}: {$field} is neither the old nor the approved one: {$post->{$field}}";
                } elseif ($post->{$field} !== $new) {
                    $changes[] = "{$label}: {$field} → {$new}";
                }
            }

            $placed = $series && (int) $post->series_id === $series->id && (int) $post->series_part === $row['part'];

            if ($post->series_id !== null && ! $placed) {
                $problems[] = "{$label}: already in another series or part";
            } elseif (! $placed) {
                $changes[] = "{$label}: into " . self::SERIES['slug'] . " as part {$row['part']}";
            }
        }

        if ($problems) {
            foreach ($problems as $problem) {
                $this->error($problem);
            }
            $this->error('Nothing written.');
            return 1;
        }

        if (! $changes) {
            $this->info('Nothing to do: already applied.');
            return 0;
        }

        foreach ($changes as $change) {
            $this->line("  {$change}");
        }

        if (! $this->option('apply')) {
            $this->warn(count($changes) . ' changes. Dry run: nothing written. Re-run with --apply to write.');
            return 0;
        }

        DB::transaction(function () use ($posts) {
            $series = Series::updateOrCreate(['slug' => self::SERIES['slug']], self::SERIES);

            foreach (self::PARTS as $row) {
                $updates = [
                    'title'       => $row['title'][1],
                    'series_id'   => $series->id,
                    'series_part' => $row['part'],
                ];

                if (isset($row['excerpt'])) {
                    $updates['excerpt'] = $row['excerpt'][1];
                }

                $posts[$row['slug']]->update($updates);
            }
        });

        $this->info('Applied ' . count($changes) . ' changes.');

        return 0;
    }
}
