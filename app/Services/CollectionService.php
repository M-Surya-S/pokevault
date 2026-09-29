<?php

namespace App\Services;

use App\Models\CollectionItem;
use Illuminate\Database\Eloquent\Collection;

class CollectionService
{
    public function __construct(
        private PokeApiService $pokeApiService,
    ) {}

    /**
     * Get all collection items.
     */
    public function all(?string $search = null, string $sortBy = 'name', string $sortDir = 'asc'): Collection
    {
        $query = CollectionItem::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['name', 'level', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';

        return $query->orderBy($sortBy, $sortDir)->get();
    }

    /**
     * Find a collection item by ID.
     */
    public function find(int $id): ?CollectionItem
    {
        return CollectionItem::find($id);
    }

    /**
     * Add a Pokémon to the collection.
     */
    public function add(int $pokemonId, ?string $nickname = null, int $level = 1, ?string $notes = null): CollectionItem
    {
        // Check for duplicate
        $existing = CollectionItem::where('pokemon_id', $pokemonId)->first();
        if ($existing) {
            throw new \RuntimeException('Pokémon sudah ada di koleksi', 409);
        }

        // Fetch data from PokéAPI
        $pokemonData = $this->pokeApiService->detail($pokemonId);

        return CollectionItem::create([
            'pokemon_id' => $pokemonData['id'],
            'name' => $pokemonData['name'],
            'image_url' => $pokemonData['sprite_url'],
            'types' => $pokemonData['types'],
            'nickname' => $nickname,
            'level' => $level,
            'notes' => $notes,
        ]);
    }

    /**
     * Update a collection item.
     */
    public function update(int $id, array $data): CollectionItem
    {
        $item = CollectionItem::findOrFail($id);

        $item->update([
            'nickname' => $data['nickname'] ?? $item->nickname,
            'level' => $data['level'] ?? $item->level,
            'notes' => $data['notes'] ?? $item->notes,
        ]);

        return $item->fresh();
    }

    /**
     * Remove a Pokémon from the collection.
     */
    public function remove(int $id): bool
    {
        $item = CollectionItem::findOrFail($id);
        return $item->delete();
    }

    /**
     * Check if a Pokémon is in the collection.
     */
    public function isCollected(int $pokemonId): bool
    {
        return CollectionItem::where('pokemon_id', $pokemonId)->exists();
    }

    /**
     * Get all collected Pokémon IDs.
     */
    public function collectedIds(): array
    {
        return CollectionItem::pluck('pokemon_id')->all();
    }
}
