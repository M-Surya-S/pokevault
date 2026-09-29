<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function __construct(
        private CollectionService $collectionService,
    ) {}

    /**
     * List all collection items.
     */
    public function index(Request $request): JsonResponse
    {
        $items = $this->collectionService->all(
            search: $request->query('search'),
            sortBy: $request->query('sort_by', 'name'),
            sortDir: $request->query('sort_dir', 'asc'),
        );

        return response()->json($items);
    }

    /**
     * Show a single collection item.
     */
    public function show(int $id): JsonResponse
    {
        $item = $this->collectionService->find($id);

        if (!$item) {
            return response()->json([
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Entri koleksi tidak ditemukan',
                ],
            ], 404);
        }

        return response()->json($item);
    }

    /**
     * Add a Pokémon to the collection.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pokemon_id' => 'required|integer|min:1',
            'nickname' => 'nullable|string|max:30',
            'level' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:200',
        ]);

        try {
            $item = $this->collectionService->add(
                pokemonId: $validated['pokemon_id'],
                nickname: $validated['nickname'] ?? null,
                level: $validated['level'] ?? 1,
                notes: $validated['notes'] ?? null,
            );

            return response()->json($item, 201);
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 400;

            return response()->json([
                'error' => [
                    'code' => $code === 409 ? 'DUPLICATE' : 'ERROR',
                    'message' => $e->getMessage(),
                ],
            ], $code);
        }
    }

    /**
     * Update a collection item.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nickname' => 'nullable|string|max:30',
            'level' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:200',
        ]);

        try {
            $item = $this->collectionService->update($id, $validated);

            return response()->json($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Entri koleksi tidak ditemukan',
                ],
            ], 404);
        }
    }

    /**
     * Remove a Pokémon from the collection.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->collectionService->remove($id);

            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Entri koleksi tidak ditemukan',
                ],
            ], 404);
        }
    }
}
