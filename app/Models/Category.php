<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'parent_id',
        'openalex_concept_id',
        'level',
    ];

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'publication_categories');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the category hierarchy.
     *
     * @return array
     */
    public function getHierarchy()
    {
        return $this->buildHierarchy($this);
    }

    /**
     * Build the category hierarchy recursively.
     *
     * @param  \Illuminate\Support\Collection  $categories
     * @return array
     */
    private function buildHierarchy()
    {
        return array_merge(
            $this->getAncestors(),
            [$this],
            $this->getDescendants()
        );
    }

    public function getAncestors(): array
    {
        $ancestors = [];

        $category = $this->parent;

        while ($category) {
            $ancestors[] = $category;
            $category = $category->parent;
        }

        return array_reverse($ancestors);
    }

    public function getDescendants(): array
    {
        $descendants = [];

        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getDescendants());
        }

        return $descendants;
    }
}
