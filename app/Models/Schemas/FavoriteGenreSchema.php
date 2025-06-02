<?php

namespace App\Models\Schemas;

/**
 * @OA\Schema(
 *     schema="FavoriteGenre",
 *     title="FavoriteGenre",
 *     description="Modelo de gênero de filme favorito",
 *     @OA\Property(property="id", type="integer", format="int64", example=1, description="ID único do gênero do favorito"),
 *     @OA\Property(property="favorite_id", type="integer", format="int64", example=1, description="ID do favorito relacionado"),
 *     @OA\Property(property="genre_id", type="integer", example=18, description="ID do gênero no TMDB"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z", description="Data de criação do registro"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z", description="Data de atualização do registro")
 * )
 */
class FavoriteGenreSchema
{
    // Esta classe é usada apenas para documentação do Swagger
}