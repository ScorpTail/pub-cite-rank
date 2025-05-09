<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorPublication extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'author_publications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'author_id',
        'publication_id',
        'author_position'
    ];
}
