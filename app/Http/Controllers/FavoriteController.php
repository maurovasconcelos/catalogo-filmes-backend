<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\FavoriteGenre;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Lista todos os favoritos.
     * Pode filtrar por gênero usando genre_id ou genre_ids.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @OA\Get(
     *     path="/favorites",
     *     operationId="getFavorites",
     *     tags={"Favoritos"},
     *     summary="Lista todos os filmes favoritos",
     *     description="Retorna uma lista de todos os filmes favoritos, com opção de filtrar por gênero",
     *     @OA\Parameter(
     *         name="genre_id",
     *         in="query",
     *         description="ID do gênero para filtrar",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="genre_ids",
     *         in="query",
     *         description="IDs dos gêneros para filtrar (múltiplos)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de favoritos recuperada com sucesso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Favorite"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Favorite::query();

        if ($request->has('genre_id')) {
            $genreId = (int) $request->genre_id;
            $favoriteIds = FavoriteGenre::where('genre_id', $genreId)
                ->pluck('favorite_id')
                ->toArray();
            $query->whereIn('id', $favoriteIds);
        }
        elseif ($request->has('genre_ids')) {
            $genreIds = is_array($request->genre_ids) 
                ? array_map('intval', $request->genre_ids) 
                : [(int) $request->genre_ids];
            
            $favoriteIds = FavoriteGenre::whereIn('genre_id', $genreIds)
                ->pluck('favorite_id')
                ->toArray();
            $query->whereIn('id', $favoriteIds);
        }

        $favorites = $query->get();

        $favorites->each(function ($favorite) {
            $favorite->genre_ids = $favorite->genreIds;
        });

        return response()->json($favorites);
    }

    /**
     * Armazena um novo favorito.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @OA\Post(
     *     path="/favorites",
     *     operationId="storeFavorite",
     *     tags={"Favoritos"},
     *     summary="Adiciona um novo filme aos favoritos",
     *     description="Armazena um novo filme na lista de favoritos",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do filme a ser adicionado aos favoritos",
     *         @OA\JsonContent(
     *             required={"tmdb_id", "title"},
     *             @OA\Property(property="tmdb_id", type="integer", example=550, description="ID do filme no TMDB"),
     *             @OA\Property(property="title", type="string", example="Clube da Luta", description="Título do filme"),
     *             @OA\Property(property="poster_path", type="string", example="/poster.jpg", description="Caminho do poster do filme"),
     *             @OA\Property(property="overview", type="string", example="Um homem deprimido que sofre de insônia...", description="Sinopse do filme"),
     *             @OA\Property(property="release_date", type="string", example="1999-10-15", description="Data de lançamento do filme"),
     *             @OA\Property(property="vote_average", type="number", format="float", example=8.4, description="Média de votos do filme"),
     *             @OA\Property(property="genre_ids", type="string", example="[18, 53, 35]", description="IDs dos gêneros do filme em formato JSON")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Filme adicionado aos favoritos com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Favorite")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Filme já está nos favoritos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Filme já está nos favoritos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
            'overview' => 'nullable|string',
            'release_date' => 'nullable|string',
            'vote_average' => 'nullable|numeric',
            'genre_ids' => 'nullable|string',
        ]);

        $existingFavorite = Favorite::where('tmdb_id', $request->tmdb_id)
            ->first();

        if ($existingFavorite) {
            return response()->json(['message' => 'Filme já está nos favoritos'], 409);
        }

        $favoriteData = $request->only([
            'tmdb_id',
            'title',
            'poster_path',
            'overview',
            'release_date',
            'vote_average',
        ]);

        $favorite = Favorite::create($favoriteData);

        $genreIds = [];
        if ($request->has('genre_ids')) {
            $genreIdsInput = $request->genre_ids;
            if (is_string($genreIdsInput) && !empty($genreIdsInput)) {
                try {
                    $genreIds = json_decode($genreIdsInput, true) ?? [];
                } catch (\Exception $e) {
                    $genreIds = [];
                }
            } elseif (is_array($genreIdsInput)) {
                $genreIds = $genreIdsInput;
            }
        }

        if (!empty($genreIds)) {
            $favorite->syncGenres($genreIds);
        }

        $favorite->genre_ids = $favorite->genreIds;

        return response()->json($favorite, 201);
    }

    /**
     * Remove um favorito.
     *
     * @param  int  $tmdb_id
     * @return \Illuminate\Http\Response
     * 
     * @OA\Delete(
     *     path="/favorites/{tmdb_id}",
     *     operationId="destroyFavorite",
     *     tags={"Favoritos"},
     *     summary="Remove um filme dos favoritos",
     *     description="Remove um filme da lista de favoritos pelo ID do TMDB",
     *     @OA\Parameter(
     *         name="tmdb_id",
     *         in="path",
     *         description="ID do filme no TMDB",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filme removido dos favoritos com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Filme removido dos favoritos com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filme não encontrado nos favoritos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Filme não encontrado nos favoritos")
     *         )
     *     )
     * )
     */
    public function destroy($tmdb_id)
    {
        $favorite = Favorite::where('tmdb_id', $tmdb_id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorito não encontrado'], 404);
        }

        $favorite->genres()->delete();
        
        $favorite->delete();

        return response()->json(['message' => 'Favorito removido com sucesso']);
    }
}