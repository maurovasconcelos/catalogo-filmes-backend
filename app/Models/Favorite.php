<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vote_average' => 'float',
    ];

    public function genres(): HasMany
    {
        return $this->hasMany(FavoriteGenre::class);
    }

    /**
     * Obtém os IDs dos gêneros associados a este favorito.
     *
     * @return array
     */
    public function getGenreIdsAttribute(): array
    {
        return $this->genres()->pluck('genre_id')->toArray();
    }

    /**
     * Sincroniza os gêneros do favorito.
     *
     * @param array $genreIds
     * @return void
     */
    public function syncGenres(array $genreIds): void
    {
        $this->genres()->delete();
        
        foreach ($genreIds as $genreId) {
            $this->genres()->create(['genre_id' => $genreId]);
        }
    }
}