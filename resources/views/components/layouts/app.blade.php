<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{ seo()->render() }}

    @stack('head')

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @filamentPWA
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>

<body class="font-sans text-base leading-normal tracking-normal text-gray-800">
    <div class="flex flex-col min-h-screen">
        <x-sections.header />

        <div class="flex-1">
            {{ $slot }}
        </div>

        <x-sections.footer />
    </div>

    @livewireScriptConfig
    @stack('scripts')
</body>

</html>
