<?php

namespace App\Livewire;

use App\Services\CollectionService;
use App\Services\PokeApiService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PokemonDetail extends Component
{
    public string|int $nameOrId;
    public array $pokemon = [];
    public bool $isCollected = false;
    public string $error = '';

    // Add to collection form
    public string $nickname = '';
    public int $level = 1;
    public string $notes = '';
    public bool $showAddForm = false;

    public function mount(string|int $nameOrId): void
    {
        $this->nameOrId = $nameOrId;
        $this->loadPokemon();
    }

    public function loadPokemon(): void
    {
        try {
            $pokeApi = app(PokeApiService::class);
            $this->pokemon = $pokeApi->detail($this->nameOrId);

            $collectionService = app(CollectionService::class);
            $this->isCollected = $collectionService->isCollected($this->pokemon['id']);
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function toggleAddForm(): void
    {
        $this->showAddForm = !$this->showAddForm;
    }

    public function addToCollection(): void
    {
        $this->validate([
            'nickname' => 'nullable|string|max:30',
            'level' => 'integer|min:1|max:100',
            'notes' => 'nullable|string|max:200',
        ]);

        try {
            $collectionService = app(CollectionService::class);
            $collectionService->add(
                pokemonId: $this->pokemon['id'],
                nickname: $this->nickname ?: null,
                level: $this->level,
                notes: $this->notes ?: null,
            );

            $this->isCollected = true;
            $this->showAddForm = false;
            $this->dispatch('notify', message: "{$this->pokemon['name']} berhasil ditambahkan ke koleksi!", type: 'success');
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.pokemon-detail')
            ->title(($this->pokemon['name'] ?? 'Detail') . ' — PokéVault');
    }
}
