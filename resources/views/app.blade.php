<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @php
        $siteDescription = 'Bernard — full-stack developer (Laravel + Vue) based in Perth, Australia.';
        $meta = $meta ?? [];
        $description = ($meta['description'] ?? null) ?: $siteDescription;
    @endphp
    <title>{{ isset($meta['title']) ? $meta['title'].' · sinfat.com' : 'sinfat.com' }}</title>
    <meta name="description" content="{{ $description }}">
    <meta property="og:title" content="{{ $meta['title'] ?? 'sinfat.com' }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="{{ $meta['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $meta['url'] ?? 'https://sinfat.com' }}">
    <meta property="og:image" content="https://sinfat.com/og-image.png">
    <meta property="og:image:width" content="2400">
    <meta property="og:image:height" content="1260">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://sinfat.com/og-image.png">
    <link rel="alternate" type="application/rss+xml" title="sinfat.com RSS" href="/feed.xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text antialiased">
    <div id="app"></div>
</body>
</html>
