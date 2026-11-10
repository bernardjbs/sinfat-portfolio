<?php

namespace App\Http\Controllers;

use App\Services\SeriesService;
use Illuminate\Http\Response;

class SeriesPageController extends Controller
{
    private const INTRO = 'Notes from building software with AI coding agents: what worked, what broke, and where the AI got it wrong.';

    public function __construct(private SeriesService $seriesService) {}

    /**
     * Serve the SPA shell for /blog/series with its own meta.
     */
    public function index(): Response
    {
        return response()->view('app', ['meta' => [
            'title'       => 'Series',
            'description' => self::INTRO,
            'url'         => rtrim(config('app.url'), '/').'/blog/series',
        ]]);
    }

    /**
     * Serve the SPA shell for /blog/series/{slug} with the series' meta, or 404 when it is not public.
     */
    public function show(string $slug): Response
    {
        $series = $this->seriesService->findPublic($slug);

        if (! $series) {
            return response()->view('app', [], 404);
        }

        return response()->view('app', ['meta' => [
            'title'       => $series->title,
            'description' => $series->pitch,
            'url'         => rtrim(config('app.url'), '/')."/blog/series/{$series->slug}",
        ]]);
    }
}
