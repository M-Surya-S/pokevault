<?php

use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\PokemonController;
use Illuminate\Support\Facades\Route;

// Pokémon (proxy PokéAPI)
Route::get('/pokemon', [PokemonController::class, 'index']);
Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'show']);

// Collection CRUD
Route::get('/collection', [CollectionController::class, 'index']);
Route::get('/collection/{id}', [CollectionController::class, 'show']);
Route::post('/collection', [CollectionController::class, 'store']);
Route::put('/collection/{id}', [CollectionController::class, 'update']);
Route::delete('/collection/{id}', [CollectionController::class, 'destroy']);
