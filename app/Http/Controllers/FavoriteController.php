<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Favorite::query();
        
        if ($request->has('genre_id')) {
            $genreId = $request->genre_id;
            $query->whereJsonContains('genre_ids', $genreId);
        } elseif ($request->has('genre_ids')) {
            $genreIds = explode(',', $request->genre_ids);
            foreach ($genreIds as $genreId) {
                $query->whereJsonContains('genre_ids', $genreId);
            }
        }
        
        $favorites = $query->get();
        
        return response()->json($favorites);
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
            'overview' => 'nullable|string',
            'release_date' => 'nullable|date',
            'vote_average' => 'nullable|numeric',
            'genre_ids' => 'nullable|string',
        ]);
        
        $existingFavorite = Favorite::where('tmdb_id', $validated['tmdb_id'])->first();
        
        if ($existingFavorite) {
            return response()->json(['message' => 'Filme já está nos favoritos'], 409);
        }
        
        if (isset($validated['genre_ids']) && !empty($validated['genre_ids'])) {
            json_decode($validated['genre_ids']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $validated['genre_ids'] = json_encode($validated['genre_ids']);
            }
        }
        
        $favorite = Favorite::create($validated);
        
        return response()->json($favorite, 201);
    }
    
    public function destroy($tmdbId): JsonResponse
    {
        $favorite = Favorite::where('tmdb_id', $tmdbId)->first();
        
        if (!$favorite) {
            return response()->json(['message' => 'Favorito não encontrado'], 404);
        }
        
        $favorite->delete();
        
        return response()->json(['message' => 'Favorito removido com sucesso']);
    }
}