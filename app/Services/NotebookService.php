<?php

namespace App\Services;

use App\Models\NotebookSnapshot;

class NotebookService
{
    // An anchor and its text; the factory's links carry no nested anchors
    private const ANCHOR = '#<a\b([^>]*)\bhref="([^"]*)"([^>]*)>(.*?)</a>#s';

    // A link between factory originals: <stamp>.html, optionally with a fragment
    private const ORIGINAL_LINK = '/^(\d{4}-\d{2}-\d{2}-\d{4})\.html(#[^"]*)?$/';

    public function find(string $stamp): ?NotebookSnapshot
    {
        // The stamp format is checked before the lookup, so a planted row with another name is never served
        if (! preg_match(NotebookSnapshot::STAMP, $stamp)) {
            return null;
        }

        return NotebookSnapshot::where('stamp', $stamp)->first();
    }

    /**
     * The published copy of a factory original, which the original never needs: local file links
     * unwrapped to their text, links to snapshots published here pointed at their page, links to any
     * other snapshot dropped with their text so none leads to a 404. Nothing but links changes.
     *
     * @return array{html: string, unwrapped: int, pointed: int, dropped: int}
     */
    public function prepare(string $stamp, string $original): array
    {
        $published = NotebookSnapshot::pluck('stamp')->push($stamp)->all();
        $counts = ['unwrapped' => 0, 'pointed' => 0, 'dropped' => 0];

        $html = preg_replace_callback(self::ANCHOR, function (array $m) use ($published, &$counts) {
            [$whole, $before, $href, $after, $text] = $m;

            if (str_starts_with($href, 'file://')) {
                $counts['unwrapped']++;
                return $text;
            }

            if (! preg_match(self::ORIGINAL_LINK, $href, $link)) {
                return $whole;
            }

            if (! in_array($link[1], $published, true)) {
                $counts['dropped']++;
                return '';
            }

            $counts['pointed']++;
            return "<a{$before}href=\"/notebook/{$link[1]}/" . ($link[2] ?? '') . "\"{$after}>{$text}</a>";
        }, $original);

        return ['html' => $html] + $counts;
    }

    /**
     * Why a copy must not be published as $stamp; empty when it may.
     *
     * @return list<string>
     */
    public function problems(string $stamp, string $html): array
    {
        $problems = [];

        if (! str_contains($html, "<title>My Software Factory, {$stamp}</title>")) {
            $problems[] = "The page's title does not name {$stamp}.";
        }

        if (stripos($html, '<script') !== false) {
            $problems[] = 'The page holds a script.';
        }

        if (str_contains($html, 'file://') || str_contains($html, '/Users/')) {
            $problems[] = 'The page holds a local file link or path.';
        }

        preg_match_all(self::ANCHOR, $html, $anchors);
        if (array_filter($anchors[2], fn ($href) => preg_match(self::ORIGINAL_LINK, $href))) {
            $problems[] = 'The page links another snapshot as <stamp>.html: it is an original, not a prepared copy.';
        }

        if ($missing = $this->missingLinks($html, $stamp)) {
            $problems[] = 'The page links snapshots missing here: ' . implode(', ', $missing) . '. Send those first.';
        }

        return $problems;
    }

    /**
     * Snapshots that $text links at /notebook/<stamp>/ which are not stored here.
     *
     * @return list<string>
     */
    public function missingLinks(string $text, ?string $self = null): array
    {
        preg_match_all('#/notebook/(\d{4}-\d{2}-\d{2}-\d{4})/#', $text, $m);
        $linked = array_values(array_diff(array_unique($m[1]), [$self]));

        return array_values(array_diff($linked, NotebookSnapshot::whereIn('stamp', $linked)->pluck('stamp')->all()));
    }

    public function store(string $stamp, string $html): NotebookSnapshot
    {
        return NotebookSnapshot::updateOrCreate(['stamp' => $stamp], ['html' => $html]);
    }
}
