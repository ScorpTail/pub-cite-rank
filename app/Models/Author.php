<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Author extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'about',
        'orcid',
        'affiliation',
        'openalex_id',
    ];

    /**
     * Get the user that owns the Author
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'author_publications');
    }

    /**
     * Get the rank associated with the Author
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rank(): HasOne
    {
        return $this->hasOne(AuthorRank::class, 'author_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
