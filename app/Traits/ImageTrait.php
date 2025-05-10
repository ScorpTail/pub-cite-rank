<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait ImageTrait
{
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'object');
    }

    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'object')
            ->where('type', 'main');
    }
}
