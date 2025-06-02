<?php

namespace Tests\Unit;

use App\Models\Favorite;
use App\Models\FavoriteGenre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteGenreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo FavoriteGenre é criado corretamente.
     */
    public function test_favorite_genre_creation(): void
    {
        // Cria um favorito
        $favorite = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste',
            'poster_path' => '/poster.jpg',
            'overview' => 'Uma descrição de teste',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);

        $favoriteGenre = FavoriteGenre::create([
            'favorite_id' => $favorite->id,
            'genre_id' => 28, // Ação
        ]);

        $this->assertDatabaseHas('favorite_genres', [
            'favorite_id' => $favorite->id,
            'genre_id' => 28,
        ]);

        $this->assertEquals(28, $favoriteGenre->genre_id);
        $this->assertEquals($favorite->id, $favoriteGenre->favorite_id);
    }

    /**
     * Testa o relacionamento entre Favorite e FavoriteGenre.
     */
    public function test_favorite_genre_relationship(): void
    {
        // Cria um favorito
        $favorite = Favorite::create([
            'tmdb_id' => 456,
            'title' => 'Filme Teste 2',
            'poster_path' => '/poster2.jpg',
            'overview' => 'Uma descrição de teste 2',
            'release_date' => '2023-07-01',
            'vote_average' => 8.5,
        ]);

        // associa generos aos favoritos
        $favorite->genres()->create(['genre_id' => 28]); // Ação
        $favorite->genres()->create(['genre_id' => 12]); // Aventura

        // Verifica se os gêneros foram associados corretamente
        $this->assertCount(2, $favorite->genres()->get());
        $genreIds = $favorite->getGenreIdsAttribute();
        sort($genreIds);
        $expectedIds = [12, 28];
        $this->assertEquals($expectedIds, $genreIds);
    }

    /**
     * Testa o método syncGenres do modelo Favorite.
     */
    public function test_sync_genres(): void
    {
        $favorite = Favorite::create([
            'tmdb_id' => 789,
            'title' => 'Filme Teste 3',
            'poster_path' => '/poster3.jpg',
            'overview' => 'Uma descrição de teste 3',
            'release_date' => '2023-08-01',
            'vote_average' => 7.8,
        ]);

        $favorite->syncGenres([28, 12]); // Ação, Aventura

        // Verifica se os gêneros foram adicionados corretamente
        $this->assertCount(2, $favorite->genres()->get());
        $genreIds = $favorite->getGenreIdsAttribute();
        sort($genreIds);
        $expectedIds = [12, 28];
        $this->assertEquals($expectedIds, $genreIds);

        $favorite->syncGenres([35, 18]); // Comédia, Drama

        $this->assertCount(2, $favorite->genres()->get());
        $genreIds = $favorite->getGenreIdsAttribute();
        sort($genreIds);
        $expectedIds = [18, 35];
        $this->assertEquals($expectedIds, $genreIds);
        $this->assertDatabaseMissing('favorite_genres', [
            'favorite_id' => $favorite->id,
            'genre_id' => 28,
        ]);
    }
}