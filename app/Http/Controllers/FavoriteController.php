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
     * @param  int  $tmdbId
     * @return \Illuminate\Http\Response
     */
    public function destroy($tmdbId)
    {
        $favorite = Favorite::where('tmdb_id', $tmdbId)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorito não encontrado'], 404);
        }

        $favorite->genres()->delete();
        
        $favorite->delete();

        return response()->json(['message' => 'Favorito removido com sucesso']);
    }
}