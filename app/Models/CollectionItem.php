<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    protected $fillable = [
        'pokemon_id',
        'name',
        'image_url',
        'types',
        'nickname',
        'level',
        'notes',
    ];

    protected $casts = [
        'types' => 'array',
        'pokemon_id' => 'integer',
        'level' => 'integer',
    ];
}
