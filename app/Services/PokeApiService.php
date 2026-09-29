<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class PokeApiService
{
    private string $baseUrl = 'https://pokeapi.co/api/v2';

    /**
     * Get paginated list of Pokémon.
     */
    public function list(int $limit = 20, int $offset = 0): array
    {
        $cacheKey = "pokemon_list_{$limit}_{$offset}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($limit, $offset) {
            $response = Http::timeout(10)->get("{$this->baseUrl}/pokemon", [
                'limit' => $limit,
                'offset' => $offset,
            ]);

            if ($response->failed()) {
                throw new \RuntimeException('PokéAPI tidak dapat dijangkau', 502);
            }

            $data = $response->json();

            // Enrich each result with ID extracted from URL
            $results = collect($data['results'])->map(function ($pokemon) {
                $segments = explode('/', rtrim($pokemon['url'], '/'));
                $id = (int) end($segments);

                return [
                    'id' => $id,
                    'name' => $pokemon['name'],
                    'url' => $pokemon['url'],
                    'image_url' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
                ];
            })->all();

            return [
                'count' => $data['count'],
                'results' => $results,
            ];
        });
    }

    /**
     * Get detailed data of a single Pokémon by name or ID.
     */
    public function detail(string|int $nameOrId): array
    {
        $nameOrId = strtolower((string) $nameOrId);
        $cacheKey = "pokemon_detail_{$nameOrId}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($nameOrId) {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/pokemon/{$nameOrId}");
            } catch (ConnectionException $e) {
                throw new \RuntimeException('PokéAPI tidak dapat dijangkau', 502);
            }

            if ($response->status() === 404) {
                throw new \RuntimeException('Pokémon tidak ditemukan', 404);
            }

            if ($response->failed()) {
                throw new \RuntimeException('PokéAPI tidak dapat dijangkau', 502);
            }

            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'height' => $data['height'],
                'weight' => $data['weight'],
                'image_url' => $data['sprites']['other']['official-artwork']['front_default']
                    ?? $data['sprites']['front_default'],
                'sprite_url' => $data['sprites']['front_default'],
                'types' => collect($data['types'])->pluck('type.name')->all(),
                'abilities' => collect($data['abilities'])->map(fn($a) => [
                    'name' => $a['ability']['name'],
                    'is_hidden' => $a['is_hidden'],
                ])->all(),
                'stats' => collect($data['stats'])->map(fn($s) => [
                    'name' => $s['stat']['name'],
                    'base_stat' => $s['base_stat'],
                ])->all(),
            ];
        });
    }

    /**
     * Search for Pokémon by name (client-side filter from cached full list).
     * PokéAPI doesn't have a search endpoint, so we fetch a large list and filter.
     */
    public function search(string $query, int $limit = 20): array
    {
        $cacheKey = 'pokemon_full_list';

        $allPokemon = Cache::remember($cacheKey, now()->addHours(24), function () {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/pokemon", [
                    'limit' => 1500,
                    'offset' => 0,
                ]);
            } catch (ConnectionException $e) {
                throw new \RuntimeException('PokéAPI tidak dapat dijangkau', 502);
            }

            if ($response->failed()) {
                throw new \RuntimeException('PokéAPI tidak dapat dijangkau', 502);
            }

            return $response->json()['results'];
        });

        $query = strtolower($query);

        $filtered = collect($allPokemon)
            ->filter(fn($p) => str_contains($p['name'], $query))
            ->take($limit)
            ->map(function ($pokemon) {
                $segments = explode('/', rtrim($pokemon['url'], '/'));
                $id = (int) end($segments);

                return [
                    'id' => $id,
                    'name' => $pokemon['name'],
                    'url' => $pokemon['url'],
                    'image_url' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
                ];
            })
            ->values()
            ->all();

        return [
            'count' => count($filtered),
            'results' => $filtered,
        ];
    }
}
