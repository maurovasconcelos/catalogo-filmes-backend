<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\FavoriteGenre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Testa se o endpoint de listagem retorna todos os favoritos.
     */
    public function test_index_returns_user_favorites(): void
    {
        // Cria favoritos
        $favorite1 = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
            'poster_path' => '/poster1.jpg',
            'overview' => 'Uma descrição de teste 1',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);
        $favorite1->syncGenres([28, 12]); // Ação, Aventura

        $favorite2 = Favorite::create([
            'tmdb_id' => 456,
            'title' => 'Filme Teste 2',
            'poster_path' => '/poster2.jpg',
            'overview' => 'Uma descrição de teste 2',
            'release_date' => '2023-07-01',
            'vote_average' => 8.5,
        ]);
        $favorite2->syncGenres([35, 18]); // Comédia, Drama

        // Faz a requisição
        $response = $this->getJson('/api/favorites');

        // Verifica se a resposta está correta
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['tmdb_id' => 123])
            ->assertJsonFragment(['tmdb_id' => 456]);
    }

    /**
     * Testa se o endpoint de listagem filtra por gênero.
     */
    public function test_index_filters_by_genre_id(): void
    {
        $favorite1 = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
            'poster_path' => '/poster1.jpg',
            'overview' => 'Uma descrição de teste 1',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);
        $favorite1->syncGenres([28, 12]); // Ação, Aventura

        $favorite2 = Favorite::create([
            'tmdb_id' => 456,
            'title' => 'Filme Teste 2',
            'poster_path' => '/poster2.jpg',
            'overview' => 'Uma descrição de teste 2',
            'release_date' => '2023-07-01',
            'vote_average' => 8.5,
        ]);
        $favorite2->syncGenres([35, 18]); // Comédia, Drama

        $response = $this->getJson('/api/favorites?genre_id=28');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['tmdb_id' => 123])
            ->assertJsonMissing(['tmdb_id' => 456]);

        // Testa filtro por múltiplos gêneros
        $response = $this->getJson('/api/favorites?genre_ids[]=35&genre_ids[]=18');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['tmdb_id' => 456])
            ->assertJsonMissing(['tmdb_id' => 123]);
    }

    /**
     * Testa se o endpoint de criação adiciona um novo favorito.
     */
    public function test_store_creates_new_favorite(): void
    {
        $favoriteData = [
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
            'poster_path' => '/poster1.jpg',
            'overview' => 'Uma descrição de teste 1',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
            'genre_ids' => json_encode([28, 12]), // Ação, Aventura
        ];

        $response = $this->postJson('/api/favorites', $favoriteData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'tmdb_id' => 123,
                'title' => 'Filme Teste 1',
            ]);

        $this->assertDatabaseHas('favorites', [
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
        ]);

        // Verifica o salvamento correto dos gêneros
        $favorite = Favorite::where('tmdb_id', 123)->first();
        $this->assertCount(2, $favorite->genres()->get());
        $this->assertDatabaseHas('favorite_genres', [
            'favorite_id' => $favorite->id,
            'genre_id' => 28,
        ]);
        $this->assertDatabaseHas('favorite_genres', [
            'favorite_id' => $favorite->id,
            'genre_id' => 12,
        ]);
    }

    /**
     * Testa se o endpoint de criação impede a adição de favoritos duplicados.
     */
    public function test_store_prevents_duplicate_favorites(): void
    {
        // Cria um favorito existente
        $favorite = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
            'poster_path' => '/poster1.jpg',
            'overview' => 'Uma descrição de teste 1',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);
        $favorite->syncGenres([28, 12]); // Ação, Aventura

        // Tenta adicionar o mesmo favorito novamente
        $favoriteData = [
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
            'poster_path' => '/poster1.jpg',
            'overview' => 'Uma descrição de teste 1',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
            'genre_ids' => json_encode([28, 12]),
        ];

        $response = $this->postJson('/api/favorites', $favoriteData);

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'Filme já está nos favoritos']);

        // Verifica se não foi criado um novo registro
        $this->assertCount(1, Favorite::where('tmdb_id', 123)->get());
    }

    /**
     * Testa se o endpoint de remoção exclui um favorito.
     */
    public function test_destroy_removes_favorite(): void
    {
        $favorite = Favorite::create([
            'tmdb_id' => 123,
            'title' => 'Filme Teste 1',
            'poster_path' => '/poster1.jpg',
            'overview' => 'Uma descrição de teste 1',
            'release_date' => '2023-06-01',
            'vote_average' => 9.0,
        ]);
        $favorite->syncGenres([28, 12]); // Ação, Aventura

        $this->assertDatabaseCount('favorite_genres', 2);

        $response = $this->deleteJson('/api/favorites/123');

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Favorito removido com sucesso']);

        $this->assertDatabaseMissing('favorites', [
            'tmdb_id' => 123,
        ]);

        $this->assertDatabaseCount('favorite_genres', 0);
    }

    /**
     * Testa se o endpoint de remoção retorna erro para favoritos inexistentes.
     */
    public function test_destroy_returns_error_for_nonexistent_favorite(): void
    {
        $response = $this->deleteJson('/api/favorites/999');
        
        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Favorito não encontrado']);
    }
}
