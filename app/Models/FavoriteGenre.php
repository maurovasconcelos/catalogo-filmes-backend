<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteGenre extends Model
{
    use HasFactory;

    protected $fillable = [
        'favorite_id',
        'genre_id',
    ];

    protected $casts = [
        'genre_id' => 'integer',
    ];

    public function favorite(): BelongsTo
    {
        return $this->belongsTo(Favorite::class);
    }
}