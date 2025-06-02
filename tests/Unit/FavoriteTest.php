<?php

namespace Tests\Unit;

use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo Favorite retorna corretamente os genre_ids como array.
     */
    public function test_genre_ids_is_cast_to_array(): void
    {
        $genreIds = [28, 12, 878]; // Ação, Aventura, Ficção Científica
        
        $favorite = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste',
            'poster_path' => '/poster.jpg',
            'overview' => 'Uma descrição de teste',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);
        
        // Adiciona gêneros manualmente
        foreach ($genreIds as $genreId) {
            $favorite->genres()->create(['genre_id' => $genreId]);
        }

        $this->assertIsArray($favorite->genre_ids);
        
        sort($genreIds);
        $returnedGenreIds = $favorite->genre_ids;
        sort($returnedGenreIds);
        
        $this->assertEquals($genreIds, $returnedGenreIds);
    }

    /**
     * Testa se o modelo Favorite converte corretamente o campo vote_average para float.
     */
    public function test_vote_average_is_cast_to_float(): void
    {
        $favorite = Favorite::create([
            'tmdb_id' => 456,
            'title' => 'Filme Teste 2',
            'poster_path' => '/poster2.jpg',
            'overview' => 'Uma descrição de teste 2',
            'release_date' => '2023-07-01',
            'vote_average' => '8.5', // String que deve ser convertida para float
        ]);

        $this->assertIsFloat($favorite->vote_average);
        $this->assertEquals(8.5, $favorite->vote_average);
    }

    /**
     * Testa se o modelo Favorite permite a atribuição em massa dos atributos corretos.
     */
    public function test_fillable_attributes(): void
    {
        $attributes = [
            'tmdb_id' => 789,
            'title' => 'Filme Teste 3',
            'poster_path' => '/poster3.jpg',
            'overview' => 'Uma descrição de teste 3',
            'release_date' => '2023-08-01',
            'vote_average' => 7.8,
        ];

        $favorite = new Favorite($attributes);
        
        foreach ($attributes as $key => $value) {
            if ($key === 'vote_average') {
                $this->assertEquals((float)$value, $favorite->$key);
            } else {
                $this->assertEquals($value, $favorite->$key);
            }
        }
    }

    /**
     * Testa o método getGenreIdsAttribute.
     */
    public function test_get_genre_ids_attribute(): void
    {
        $favorite = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste',
            'poster_path' => '/poster.jpg',
            'overview' => 'Uma descrição de teste',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);

        // Inicialmente não deve ter gêneros
        $this->assertEmpty($favorite->getGenreIdsAttribute());

        $favorite->genres()->create(['genre_id' => 28]); // Ação
        $favorite->genres()->create(['genre_id' => 12]); // Aventura

        $genreIds = $favorite->getGenreIdsAttribute();
        $this->assertCount(2, $genreIds);
        $this->assertContains(28, $genreIds);
        $this->assertContains(12, $genreIds);
    }
}
