<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PokéVault - Jelajahi dan kelola koleksi Pokémon favoritmu">
    <title>{{ $title ?? 'PokéVault' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen bg-base-200">
    {{-- Navbar --}}
    <div class="navbar bg-base-100 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex items-center justify-between w-full">
            <div class="flex-1">
                <a href="/" class="btn btn-ghost text-xl font-bold gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-8 h-8">
                        <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="4"/>
                        <line x1="2" y1="50" x2="98" y2="50" stroke="currentColor" stroke-width="4"/>
                        <circle cx="50" cy="50" r="16" fill="none" stroke="currentColor" stroke-width="4"/>
                        <circle cx="50" cy="50" r="8" fill="currentColor"/>
                    </svg>
                    PokéVault
                </a>
            </div>
            <div class="flex-none gap-2">
                <a href="/" class="btn btn-ghost btn-sm {{ request()->is('/') ? 'btn-active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Jelajah
                </a>
                <a href="/collection" class="btn btn-ghost btn-sm {{ request()->is('collection*') ? 'btn-active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Koleksi Saya
                </a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <main class="container mx-auto px-4 py-6 flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="footer footer-center p-4 bg-base-100 text-base-content mt-auto">
        <aside>
            <p>PokéVault &copy; {{ date('Y') }} — Data dari <a href="https://pokeapi.co" class="link link-primary" target="_blank">PokéAPI</a></p>
        </aside>
    </footer>

    @livewireScripts
</body>
</html>
