<?php

namespace App\Livewire;

use App\Services\CollectionService;
use App\Services\PokeApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Jelajah Pokémon — PokéVault')]
class PokemonList extends Component
{
    #[Url]
    public string $search = '';

    public int $offset = 0;
    public int $limit = 20;
    public array $pokemon = [];
    public int $totalCount = 0;
    public bool $loading = false;
    public string $error = '';

    public function mount(): void
    {
        $this->loadPokemon();
    }

    public function loadPokemon(): void
    {
        $this->error = '';

        try {
            $pokeApi = app(PokeApiService::class);

            if ($this->search) {
                $data = $pokeApi->search($this->search, $this->limit);
            } else {
                $data = $pokeApi->list($this->limit, $this->offset);
            }

            $this->pokemon = $data['results'];
            $this->totalCount = $data['count'];
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
            $this->pokemon = [];
        }
    }

    public function updatedSearch(): void
    {
        $this->offset = 0;
        $this->loadPokemon();
    }

    public function nextPage(): void
    {
        if ($this->offset + $this->limit < $this->totalCount) {
            $this->offset += $this->limit;
            $this->loadPokemon();
        }
    }

    public function prevPage(): void
    {
        if ($this->offset - $this->limit >= 0) {
            $this->offset -= $this->limit;
            $this->loadPokemon();
        }
    }

    public function goToPage(int $page): void
    {
        $this->offset = ($page - 1) * $this->limit;
        $this->loadPokemon();
    }

    public function addToCollection(int $pokemonId): void
    {
        try {
            $collectionService = app(CollectionService::class);
            $collectionService->add($pokemonId);

            $this->dispatch('notify', message: 'Pokémon berhasil ditambahkan ke koleksi!', type: 'success');
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $collectionService = app(CollectionService::class);
        $collectedIds = $collectionService->collectedIds();

        $currentPage = (int) floor($this->offset / $this->limit) + 1;
        $totalPages = $this->search ? 1 : (int) ceil($this->totalCount / $this->limit);

        return view('livewire.pokemon-list', [
            'collectedIds' => $collectedIds,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
}
