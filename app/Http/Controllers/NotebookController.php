<?php

namespace App\Http\Controllers;

use App\Services\NotebookService;
use Symfony\Component\HttpFoundation\Response;

class NotebookController extends Controller
{
    public function __construct(private NotebookService $notebookService) {}

    /**
     * Serve a published factory notebook snapshot, a static page stored in the database so a new one
     * reaches production without a deploy (just notebook-to-prod).
     */
    public function __invoke(string $stamp): Response
    {
        $snapshot = $this->notebookService->find($stamp);

        if (! $snapshot) {
            return response()->view('app', [], 404);
        }

        return response($snapshot->html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            // Imported pages skip code review, so none may run code on this site
            'Content-Security-Policy' => "script-src 'none'; object-src 'none'; base-uri 'none'",
        ]);
    }
}
