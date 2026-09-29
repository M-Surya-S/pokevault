<x-layouts.app>
    <x-slot:title>
        Terjadi Kesalahan — PokéVault
    </x-slot>

    <div class="flex flex-col items-center justify-center py-20 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-error/50 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h1 class="text-6xl font-extrabold text-error mb-2">500</h1>
        <h2 class="text-2xl font-bold mb-4">Terjadi Kesalahan Server</h2>
        <p class="text-base-content/70 max-w-md mb-8">
            Wah, sepertinya ada sedikit masalah di server kami. Silakan coba beberapa saat lagi.
        </p>
        <a href="/" wire:navigate class="btn btn-outline btn-error gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
            </svg>
            Segarkan Halaman
        </a>
    </div>
</x-layouts.app>
