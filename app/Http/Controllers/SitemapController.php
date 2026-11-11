<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function __construct(private SitemapService $sitemapService) {}

    public function __invoke(Request $request): Response
    {
        return $this->sitemapService->build()->toResponse($request);
    }
}
