<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PokeApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function __construct(
        private PokeApiService $pokeApiService,
    ) {}

    /**
     * List Pokémon (proxy PokéAPI).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query('limit', 20);
            $offset = (int) $request->query('offset', 0);
            $search = $request->query('search');

            if ($search) {
                $data = $this->pokeApiService->search($search, $limit);
            } else {
                $data = $this->pokeApiService->list($limit, $offset);
            }

            return response()->json($data);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => [
                    'code' => 'POKEAPI_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], $e->getCode() ?: 502);
        }
    }

    /**
     * Detail of a single Pokémon (proxy PokéAPI).
     */
    public function show(string $nameOrId): JsonResponse
    {
        try {
            $data = $this->pokeApiService->detail($nameOrId);

            return response()->json($data);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 502;

            return response()->json([
                'error' => [
                    'code' => $code === 404 ? 'NOT_FOUND' : 'POKEAPI_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], $code);
        }
    }
}
