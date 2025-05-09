<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorRank extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'author_ranks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'author_id',
        'total_publications',
        'total_citations',
        'h_index',
        'rank_score'
    ];

    /**
     * Get the author that owns the rank.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }
}
