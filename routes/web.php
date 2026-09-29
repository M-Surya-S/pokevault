<?php

use Illuminate\Support\Facades\Route;

// Jelajah Pokémon (home)
Route::get('/', App\Livewire\PokemonList::class)->name('pokemon.index');

// Detail Pokémon
Route::get('/pokemon/{nameOrId}', App\Livewire\PokemonDetail::class)->name('pokemon.detail');

// Koleksi Saya
Route::get('/collection', App\Livewire\CollectionList::class)->name('collection.index');
