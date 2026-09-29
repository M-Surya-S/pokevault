<div>
    {{-- Toast Notification --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="toast toast-bottom toast-end z-[100] mb-4 mr-4"
        style="display: none;"
    >
        <div class="alert" :class="type === 'success' ? 'alert-success' : 'alert-error'">
            <span x-text="message"></span>
        </div>
    </div>

    {{-- Hero Section --}}
    <div class="hero bg-gradient-to-br from-primary/20 via-base-200 to-secondary/20 rounded-2xl mb-8 py-12">
        <div class="hero-content text-center">
            <div class="max-w-2xl">
                <h1 class="text-4xl font-extrabold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                    Jelajahi Pokémon
                </h1>
                <p class="text-base-content/70 mb-6">Temukan dan tambahkan Pokémon favoritmu ke koleksi</p>

                {{-- Search --}}
                <div class="join w-full max-w-md mx-auto">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari Pokémon..."
                        class="input input-bordered join-item w-full"
                        id="search-pokemon"
                    />
                    @if($search)
                        <button wire:click="$set('search', '')" class="btn join-item btn-ghost">
                            ✕
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading wire:target="loadPokemon, search, nextPage, prevPage, goToPage" class="flex justify-center py-12">
        <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

    {{-- Error State --}}
    @if($error)
        <div class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $error }}</span>
            <button wire:click="loadPokemon" class="btn btn-sm btn-ghost">Coba Lagi</button>
        </div>
    @endif

    {{-- Pokemon Grid --}}
    <div wire:loading.remove wire:target="loadPokemon, search, nextPage, prevPage, goToPage">
        @if(count($pokemon) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($pokemon as $poke)
                    <div class="card bg-base-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                        <a href="{{ route('pokemon.detail', $poke['name']) }}" wire:navigate>
                            <figure class="px-4 pt-6 pb-2 relative">
                                {{-- Collected Badge --}}
                                @if(in_array($poke['id'], $collectedIds))
                                    <div class="badge badge-success badge-sm absolute top-2 right-2 gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Koleksi
                                    </div>
                                @endif

                                <img
                                    src="{{ $poke['image_url'] }}"
                                    alt="{{ $poke['name'] }}"
                                    class="w-24 h-24 object-contain group-hover:scale-110 transition-transform duration-300"
                                    loading="lazy"
                                />
                            </figure>
                            <div class="card-body items-center text-center p-3 pt-0">
                                <span class="text-xs text-base-content/50">#{{ str_pad($poke['id'], 3, '0', STR_PAD_LEFT) }}</span>
                                <h2 class="card-title text-sm capitalize">{{ $poke['name'] }}</h2>
                            </div>
                        </a>

                        {{-- Quick Add Button --}}
                        @if(!in_array($poke['id'], $collectedIds))
                            <div class="card-actions justify-center pb-3 px-3">
                                <button
                                    wire:click="addToCollection({{ $poke['id'] }})"
                                    wire:loading.attr="disabled"
                                    class="btn btn-primary btn-xs btn-outline gap-1 w-full"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if(!$search && $totalPages > 1)
                <div class="flex justify-center mt-8">
                    <div class="join">
                        <button
                            wire:click="prevPage"
                            class="join-item btn btn-sm"
                            @if($currentPage <= 1) disabled @endif
                        >
                            «
                        </button>

                        @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                            <button
                                wire:click="goToPage({{ $i }})"
                                class="join-item btn btn-sm {{ $i === $currentPage ? 'btn-active' : '' }}"
                            >
                                {{ $i }}
                            </button>
                        @endfor

                        <button
                            wire:click="nextPage"
                            class="join-item btn btn-sm"
                            @if($currentPage >= $totalPages) disabled @endif
                        >
                            »
                        </button>
                    </div>
                </div>
                <p class="text-center text-sm text-base-content/50 mt-2">
                    Halaman {{ $currentPage }} dari {{ $totalPages }} ({{ $totalCount }} Pokémon)
                </p>
            @endif

        @else
            {{-- Empty State --}}
            @if(!$error)
                <div class="flex flex-col items-center justify-center py-16 text-base-content/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-lg font-medium">Pokémon tidak ditemukan</p>
                    <p class="text-sm">Coba kata kunci lain</p>
                </div>
            @endif
        @endif
    </div>
</div>
