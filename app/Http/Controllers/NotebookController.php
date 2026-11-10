<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class NotebookController extends Controller
{
    /**
     * Serve a published factory notebook snapshot, a static page kept in resources/notebook.
     * Through Laravel rather than public/, so the URL works whatever the web server does with folders.
     */
    public function __invoke(string $stamp): Response
    {
        // The stamp format is the only thing that reaches the file path
        $path = resource_path("notebook/{$stamp}.html");

        if (! preg_match('/^\d{4}-\d{2}-\d{2}-\d{4}$/', $stamp) || ! is_file($path)) {
            return response()->view('app', [], 404);
        }

        return response()->file($path, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
