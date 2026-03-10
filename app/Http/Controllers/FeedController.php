<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $lastBuildDate = $posts->first()?->published_at?->toRfc2822String() ?? now()->toRfc2822String();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '  <title>sinfat.com</title>' . "\n";
        $xml .= '  <link>https://sinfat.com/blog</link>' . "\n";
        $xml .= '  <description>Technical blog by Bernard — Laravel, AI, Vue, and developer tools.</description>' . "\n";
        $xml .= '  <language>en-au</language>' . "\n";
        $xml .= '  <lastBuildDate>' . $lastBuildDate . '</lastBuildDate>' . "\n";
        $xml .= '  <atom:link href="https://sinfat.com/feed.xml" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($posts as $post) {
            $link = 'https://sinfat.com/blog/' . $post->slug;
            $xml .= '  <item>' . "\n";
            $xml .= '    <title>' . e($post->title) . '</title>' . "\n";
            $xml .= '    <link>' . $link . '</link>' . "\n";
            $xml .= '    <guid isPermaLink="true">' . $link . '</guid>' . "\n";
            $xml .= '    <pubDate>' . $post->published_at->toRfc2822String() . '</pubDate>' . "\n";
            if ($post->excerpt) {
                $xml .= '    <description>' . e($post->excerpt) . '</description>' . "\n";
            }
            if ($post->category) {
                $xml .= '    <category>' . e($post->category) . '</category>' . "\n";
            }
            $xml .= '  </item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
