<?php

namespace App\Models\Schemas;

/**
 * @OA\Schema(
 *     schema="Favorite",
 *     title="Favorite",
 *     description="Modelo de filme favorito",
 *     @OA\Property(property="id", type="integer", format="int64", example=1, description="ID único do favorito"),
 *     @OA\Property(property="tmdb_id", type="integer", example=550, description="ID do filme no TMDB"),
 *     @OA\Property(property="title", type="string", example="Clube da Luta", description="Título do filme"),
 *     @OA\Property(property="poster_path", type="string", example="/poster.jpg", description="Caminho do poster do filme"),
 *     @OA\Property(property="overview", type="string", example="Um homem deprimido que sofre de insônia...", description="Sinopse do filme"),
 *     @OA\Property(property="release_date", type="string", format="date", example="1999-10-15", description="Data de lançamento do filme"),
 *     @OA\Property(property="vote_average", type="number", format="float", example=8.4, description="Média de votos do filme"),
 *     @OA\Property(property="genre_ids", type="array", @OA\Items(type="integer"), example="[18, 53, 35]", description="IDs dos gêneros do filme"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z", description="Data de criação do registro"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z", description="Data de atualização do registro")
 * )
 */
class FavoriteSchema
{
    
}