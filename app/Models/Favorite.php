<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tmdb_id',
        'title',
        'poster_path',
        'overview',
        'release_date',
        'vote_average',
        'user_id',
        'genre_ids', 
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vote_average' => 'float',
        'genre_ids' => 'array',
    ];
}