<x-layouts.app>
    <x-slot:title>
        Halaman Tidak Ditemukan — PokéVault
    </x-slot>

    <div class="flex flex-col items-center justify-center py-20 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-base-content/30 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="text-6xl font-extrabold text-primary mb-2">404</h1>
        <h2 class="text-2xl font-bold mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-base-content/70 max-w-md mb-8">
            Waduh! Sepertinya Snorlax menghalangi jalan. Halaman yang Anda cari tidak ada atau mungkin sudah dipindahkan.
        </p>
        <a href="/" wire:navigate class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</x-layouts.app>
