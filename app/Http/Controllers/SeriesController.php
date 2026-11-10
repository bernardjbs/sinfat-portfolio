<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeriesResource;
use App\Services\SeriesService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SeriesController extends Controller
{
    public function __construct(private SeriesService $seriesService) {}

    public function index(): AnonymousResourceCollection
    {
        return SeriesResource::collection($this->seriesService->listPublic());
    }

    public function show(string $slug): SeriesResource
    {
        $series = $this->seriesService->findPublic($slug);

        abort_unless($series, 404, 'Series not found');

        return new SeriesResource($series);
    }
}
