<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>sinfat.com</title>
    <meta name="description" content="Bernard — full-stack developer (Laravel + Vue) based in Perth, Australia.">
    <meta property="og:title" content="sinfat.com">
    <meta property="og:description" content="Bernard — full-stack developer (Laravel + Vue) based in Perth, Australia.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sinfat.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text antialiased">
    <div id="app"></div>
</body>
</html>
