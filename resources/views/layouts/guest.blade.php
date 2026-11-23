<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#059669">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50">
        <div class="min-h-screen overflow-x-hidden flex flex-col sm:justify-center items-center px-4 py-6 sm:py-0">
            <a href="/" class="flex items-center gap-2 min-h-[44px]">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                    <x-application-logo class="h-6 w-6 fill-current" />
                </span>
                <span class="text-lg font-bold text-slate-800">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <div class="w-full sm:max-w-md mt-6 p-5 sm:p-6 bg-white border border-slate-200/80 shadow-sm rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
